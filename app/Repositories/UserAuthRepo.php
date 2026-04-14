<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Facades\OrderItemFacade;
use App\Repositories\Interfaces\UserAuthInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserAuthRepo implements UserAuthInterface
{
    public function __construct(private User $model) {}

    public function register(array $data): User|null
    {
        try {
            $user = $this->model->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'otp' => random_int(1000, 9999),
                'clean_email' => $data['clean_email'],
            ]);
            // $user->createToken('externalApi', ['external-api'])->accessToken;
            Cache::put('otp_attempts_' . $user->email, 1, now()->addMinutes(60));
            return $user;
        } catch (\Throwable $th) {
            Log::error('Registration error: ' . $th->getMessage());
            return null;
        }
    }

    public function login(string $email, string $password): mixed
    {
        $user = $this->model->where('email', $email)->first();
        if (!$user ||  !Hash::check($password, $user->password)) {
            return 'Invalid credentials';
        }
        if (is_null($user->email_verified_at)) {
            return 'Email not verified';
        }
        $token = $user->createToken('authToken', ['user-api'])->accessToken;
        $user->token = $token;
        $user->count_pending_numbers = OrderItemFacade::countPendingNumbers($user->id);
        return $user;
    }

    public function logout(Request $request): bool
    {
        $request->user()->token()->revoke();
        return true;
    }

    public function refreshToken(Request $request): mixed
    {
        $user = $request->user();
        $user->token()->revoke();
        $token = $user->createToken('authToken', ['user-api'])->accessToken;
        $user->token = $token;
        return $user;
    }

    public function forgotPassword(string $email): ?string
    {
        $user = $this->model->where('email', $email)->first();
        if (!$user) {
            return null;
        }
        $status = Password::sendResetLink(['email' => $email], function ($user, $token) {
            $user->notify(new \App\Notifications\ResetPasswordNotification($user, $token));
        });
        if ($status !== Password::RESET_LINK_SENT) {
            return null;
        }

        return 'Reset link sent to your email';
    }

    public function resetPassword(string $email, string $password, string $token): string|null
    {
        $status = Password::reset(
            ['email' => $email, 'password' => $password, 'token' => $token],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60)
                ]);

                $user->save();
            }
        );
        if ($status !== Password::PASSWORD_RESET) {
            return null;
        }
        return 'Password reset successfully';
    }

    public function verifyEmail(int $otp, string $email): ?string
    {
        $user = $this->model->where('otp', $otp)
            ->where('email', $email)
            ->where('created_at', '>=', now()->subMinutes(30)) // OTP valid for 30 minutes
            ->first();
        if (!$user) {
            return null;
        }
        $user->email_verified_at = now();
        $user->otp = null;
        $user->save();
        return 'Email verified successfully';
    }

    public function getUserProfile(Request $request): ?User
    {
        $user = $request->user();
        if (!$user) {
            return null;
        }
        $user->count_pending_numbers = OrderItemFacade::countPendingNumbers($user->id);
        return $user;
    }

    public function regenerateOtp(string $email): ?User
    {
        $user = $this->model->where('email', $email)->first();
        if (!$user) {
            return null;
        }
        $user->otp = random_int(1000, 9999);
        $user->created_at = now();
        $user->save();
        return $user;
    }

    public function rotateApiKey(Request $request): string
    {
        $user = $request->user();

        $user->tokens()
            ->where('name', 'externalApi')
            ->update(['revoked' => true]);

        $newToken = $user->createToken('externalApi', ['external-api'])->accessToken;

        return $newToken;
    }
}

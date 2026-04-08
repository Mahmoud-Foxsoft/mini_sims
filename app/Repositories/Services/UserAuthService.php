<?php

namespace App\Repositories\Services;

use App\Jobs\EmailVerifyNotificationJob;
use App\Repositories\Interfaces\UserAuthInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserAuthService
{
    public function __construct(protected UserAuthInterface $userAuthRepository) {}

    public function register(array $data)
    {
        return $this->userAuthRepository->register($data);
    }

    public function login(string $email, string $password)
    {
        return $this->userAuthRepository->login($email, $password);
    }

    public function logout(Request $request)
    {
        return $this->userAuthRepository->logout($request);
    }

    public function refreshToken(Request $request)
    {
        return $this->userAuthRepository->refreshToken($request);
    }

    public function getUserProfile(Request $request)
    {
        return $this->userAuthRepository->getUserProfile($request);
    }

    public function forgotPassword(string $email)
    {
        if ($throttleError = $this->throttleOtp($email)) {
            return ['error' => $throttleError];
        }

        return $this->userAuthRepository->forgotPassword($email);
    }

    public function resetPassword(string $email, string $password, string $token)
    {
        return $this->userAuthRepository->resetPassword($email, $password, $token);
    }

    public function verifyEmail(int $otp, string $email)
    {
        return $this->userAuthRepository->verifyEmail($otp, $email);
    }

    public function resendOtp(string $email): array|bool
    {
        if ($throttleError = $this->throttleOtp($email)) {
            return ['error' => $throttleError];
        }
        $user = $this->userAuthRepository->regenerateOtp($email);
        if (! $user) {
            return false;
        }
        EmailVerifyNotificationJob::dispatch($user);

        return true;
    }

    private function throttleOtp(string $email): ?string
    {
        $today = now()->toDateString();
        $countKey = 'otp_daily_count_'.$email.'_'.$today;
        $lastKey = 'otp_last_sent_'.$email;

        $count = Cache::get($countKey, 0);
        if ($count >= 5) {
            return 'Maximum OTP requests reached for today.';
        }

        $lastSent = Cache::get($lastKey);
        if ($lastSent && (time() - $lastSent) < 30) {
            return 'Please wait 30 seconds before requesting another OTP.';
        }

        Cache::put($lastKey, time(), now()->addDay());
        Cache::put($countKey, $count + 1, now()->endOfDay());

        return null;
    }

    public function rotateApiKey(Request $request)
    {
        return $this->userAuthRepository->rotateApiKey($request);
    }
}

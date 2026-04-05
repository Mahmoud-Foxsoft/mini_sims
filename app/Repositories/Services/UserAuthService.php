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
        if (Cache::get('otp_attempts_' . $email, 1) > env('MAX_EMAIL_ATTEMPTS', 3)) {
            return false; // Exceeded max attempts
        }
        Cache::increment('otp_attempts_' . $email, 1, now()->addMinutes(60));
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

    public function resendOtp(string $email): bool
    {
        if (Cache::get('otp_attempts_' . $email, 1) > env('MAX_EMAIL_ATTEMPTS', 3)) {
            return false; // Exceeded max attempts
        }
        $user = $this->userAuthRepository->regenerateOtp($email);
        if (!$user) {
            return false;
        }
        EmailVerifyNotificationJob::dispatch($user);
        return true;
    }
}

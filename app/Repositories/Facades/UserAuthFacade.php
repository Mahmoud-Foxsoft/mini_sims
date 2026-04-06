<?php

namespace App\Repositories\Facades;

use App\Repositories\Services\UserAuthService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed register(array $data)
 * @method static mixed login(string $email, string $password)
 * @method static mixed logout(\Illuminate\Http\Request $request)
 * @method static mixed refreshToken(\Illuminate\Http\Request $request)
 * @method static mixed getUserProfile(\Illuminate\Http\Request $request)
 * @method static mixed forgotPassword(string $email)
 * @method static mixed resetPassword(string $email, string $password, string $token)
 * @method static mixed verifyEmail(int $otp, string $email)
 * @method static bool resendOtp(string $email)
 * @method static string rotateApiKey(\Illuminate\Http\Request $request)
 * @see \App\Repositories\Services\UserAuthService
 */
class UserAuthFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return UserAuthService::class;
    }
}

<?php

namespace App\Repositories\Facades;

use App\Repositories\Interfaces\AdminAuthInterface;
use App\Repositories\Services\AdminAuthService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed login(string $email, string $password)
 * @method static bool logout(Request $request)
 * @method static mixed refreshToken(Request $request)
 * @method static Admin|null getAdminProfile(Request $request)
 *
 * @see App\Repositories\Interfaces\AdminAuthInterface
 */
class AdminAuthFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AdminAuthInterface::class;
    }
}

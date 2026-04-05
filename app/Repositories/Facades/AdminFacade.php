<?php


namespace App\Repositories\Facades;

use App\Repositories\Services\AdminService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed getAllAdmins(?array $filters = null)
 * @method static mixed createAdmin(string $name, string $email, string $password)
 * @method static mixed updateAdmin(Admin $admin, ?string $name = null, ?string $email = null, ?string $password = null)
 * @method static mixed deleteAdmin(mixed $id)
 * @see \App\Repositories\Services\AdminService
 */
class AdminFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AdminService::class;
    }
}

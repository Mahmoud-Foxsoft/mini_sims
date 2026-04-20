<?php

namespace App\Repositories\Facades;

use App\Repositories\Services\UserService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\User|null find(int $id)
 * @method static bool update(int $id, array $data)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filter(array $filters)
 * @method static bool addBalance(\App\Models\User $user, float $amount)
 * @method static bool checkBalance(\App\Models\User $user, float $amount)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator getForSelect(string $email, int $perPage = 10)
 * @method static int sumTotalUsersMonthly()
 * @see \App\Repositories\Services\UserService
 */
class UserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return UserService::class;
    }
}

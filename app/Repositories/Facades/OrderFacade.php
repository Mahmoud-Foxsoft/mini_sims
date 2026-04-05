<?php

namespace App\Repositories\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator getAllOrdersForUserPlan(\App\Models\UserPlan $user_plan, array $filters)
 * @method static \App\Models\Order|null createNewOrder(array $data)
 * @method static Collection sumOrdersMonthly(?Carbon $from, ?Carbon $to, ?array $user_plan_ids, ?int $user_id)
 * @see \App\Repositories\Services\OrderService
 */
class OrderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Repositories\Services\OrderService';
    }
}

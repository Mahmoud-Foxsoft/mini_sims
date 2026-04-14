<?php

namespace App\Repositories\Facades;

use App\Repositories\Services\OrderItemService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed all(array $filters = [])
 * @method static mixed find(int $id)
 * @method static mixed create(array $data)
 * @method static mixed update(OrderItem $orderItem, array $data)
 * @method static mixed delete(OrderItem $orderItem)
 * @method static mixed cancel(OrderItem $orderItem)
 * @method static mixed countPendingNumbers(int $user_id)
 * 
 * @see \App\Repositories\Services\OrderItemService
 */
class OrderItemFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return OrderItemService::class;
    }
}

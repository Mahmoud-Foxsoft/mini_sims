<?php

namespace App\Repositories\Services;

use App\Models\Order;
use App\Models\UserPlan;
use App\Repositories\Interfaces\OrderInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function __construct(private OrderInterface $repo) {}

    public function getAllOrders(array $filters): LengthAwarePaginator
    {
        return $this->repo->getAllOrders($filters);
    }

    public function createNewOrder(array $data): ?Order
    {
        return $this->repo->createNewOrder($data);
    }

    public function sumOrdersMonthly(?Carbon $from = null, ?Carbon $to = null, ?int $user_id = null)
    {
        return $this->repo->sumOrdersMonthly($from, $to, $user_id);
    }
}

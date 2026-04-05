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

    public function getAllOrdersForUserPlan(UserPlan $user_plan, array $filters): LengthAwarePaginator
    {
        return $this->repo->getAllOrdersForUserPlan($user_plan, $filters);
    }

    public function createNewOrder(array $data): ?Order
    {
        return $this->repo->createNewOrder($data);
    }

    public function sumOrdersMonthly(?Carbon $from = null, ?Carbon $to = null, ?array $user_plan_ids = null, ?int $user_id = null)
    {
        return $this->repo->sumOrdersMonthly($from, $to, $user_plan_ids, $user_id);
    }
}

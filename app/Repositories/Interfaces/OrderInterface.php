<?php

namespace App\Repositories\Interfaces;

use App\Models\Order;
use App\Models\UserPlan;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderInterface
{
    public function getAllOrders(array $filters): LengthAwarePaginator;
    public function createNewOrder(array $data): ?Order;
    public function sumOrdersMonthly(?Carbon $from, ?Carbon $to, ?int $user_id);
}

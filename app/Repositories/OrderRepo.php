<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\UserPlan;
use App\Repositories\Interfaces\OrderInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderRepo implements OrderInterface
{

    /**
     * Retrieve a paginated list of orders for the given user plan.
     *
     * This query is scoped to the specified UserPlan and supports optional filters:
     * - 'created_at' (string|\Carbon\Carbon|null): Filter by the order creation date.
     * - 'gb_amount'  (int|float|null): Filter by the exact GB amount.
     * - 'per_page'   (int|null): Number of results per page (default: 10).
     *
     * @param  \App\Models\UserPlan  $user_plan  The user plan whose orders should be retrieved.
     * @param  array<string, mixed>  $filters    An associative array of optional filters.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Order>
     */
    public function getAllOrdersForUserPlan(UserPlan $user_plan, array $filters): LengthAwarePaginator
    {
        return $user_plan->orders()
            ->when(
                $filters['created_at'] ?? null,
                function ($query) use ($filters) {
                    $date = Carbon::parse($filters['created_at'])->format('Y-m-d');
                    $query->whereDate('created_at', $date);
                }
            )->when(
                $filters['gb_amount'] ?? null,
                fn($query, $amount) => $query->where('gb_amount', $amount)
            )->latest()->paginate(10);
    }

    /**
     * Create a new order record.
     *
     * Attempts to insert a new Order into the database.
     * Returns the created model instance on success, or null if an error occurs.
     * Any database errors are logged.
     *
     * @param  array $data  The data for creating the order:
     *                      - 'user_plan_id' (int): The ID of the related user plan.
     *                      - 'gb_amount'    (float|int): The amount of GB purchased.
     *                      - 'price_per_gb' (float): The price per GB.
     *
     * @return \App\Models\Order|null  The created Order instance, or null on failure.
     */
    public function createNewOrder(array $data): ?Order
    {
        try {
            return Order::create([
                'user_plan_id' => $data['user_plan_id'],
                'gb_amount' => $data['gb_amount'],
                'price_per_gb' => $data['price_per_gb'],
            ]);
        } catch (\Throwable $th) {
            Log::error('Db Create Error', [$th->getMessage()]);
            return null;
        }
    }

    public function sumOrdersMonthly(?Carbon $from, ?Carbon $to, ?array $user_plan_ids, ?int $user_id)
    {
        return Cache::remember('sum.orders_' . $from?->toDateString() . '_' . $to?->toDateString() . '_' . $user_id, 3600, function () use ($user_plan_ids, $from, $to) {
            return DB::table('orders')
                ->join('user_plans', 'orders.user_plan_id', '=', 'user_plans.id')
                ->join('plans', 'user_plans.plan_id', '=', 'plans.id')
                ->where('user_plans.status', 'active')
                ->when($from, fn($query) => $query->whereDate('orders.created_at', '>=', $from->toDateString()))
                ->when($to, fn($query) => $query->whereDate('orders.created_at', '<=', $to->toDateString()))
                ->selectRaw('plans.id,SUM(orders.gb_amount) as total_gb, SUM(orders.price_per_gb * orders.gb_amount) as total_price')
                ->groupBy('plans.id')
                ->get();
        });
    }
}

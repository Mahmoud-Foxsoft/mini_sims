<?php

namespace App\Repositories;

use App\Models\Order;
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
    public function getAllOrders(array $filters): LengthAwarePaginator
    {
        return Order::when(
            $filters['user_id'] ?? null,
            fn($query, $user_id) => $query->where('user_id', $user_id)
        )
            ->when(
                $filters['with_user'] ?? null,
                fn($query) => $query->with('user')
            )
            ->when(
                $filters['created_at'] ?? null,
                function ($query) use ($filters) {
                    $date = Carbon::parse($filters['created_at'])->format('Y-m-d');
                    $query->whereDate('created_at', $date);
                }
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
                'user_id' => $data['user_id'],
                'total_cent_price' => $data['total_cent_price'],
                'status' => $data['status'] ?? 'pending'
            ]);
        } catch (\Throwable $th) {
            Log::error('Db Create Error', [$th->getMessage()]);
            return null;
        }
    }

    public function sumOrdersMonthly(?Carbon $from, ?Carbon $to, ?int $user_id)
    {
        return Cache::remember('sum.orders_' . $from?->toDateString() . '_' . $to?->toDateString() . '_' . $user_id, 3600, function () use ($from, $to) {
            $totalAmount = DB::table('orders')
                ->where('status', 'completed')
                ->when($from, fn($query) => $query->whereDate('orders.created_at', '>=', $from->toDateString()))
                ->when($to, fn($query) => $query->whereDate('orders.created_at', '<=', $to->toDateString()))
                ->selectRaw('SUM(orders.total_cent_price) as total_amount')
                ->get(['total_amount']);
            return (int) $totalAmount[0]->total_amount;
        });
    }
}

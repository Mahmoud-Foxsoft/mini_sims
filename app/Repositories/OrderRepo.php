<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Facades\OrderItemFacade;
use App\Repositories\Facades\TransactionFacade;
use App\Repositories\Interfaces\OrderInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
     * @param  UserPlan  $user_plan  The user plan whose orders should be retrieved.
     * @param  array<string, mixed>  $filters  An associative array of optional filters.
     * @return LengthAwarePaginator<Order>
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
            )
            ->when(
                $filters['status'] ?? null,
                fn($query, $status) => $query->where('status', $status)
            )->latest()->paginate(10);
    }

    /**
     * Create a new order record.
     *
     * Attempts to insert a new Order into the database.
     * Returns the created model instance on success, or null if an error occurs.
     * Any database errors are logged.
     *
     * @param  array  $data  The data for creating the order:
     *                       - 'user_plan_id' (int): The ID of the related user plan.
     *                       - 'gb_amount'    (float|int): The amount of GB purchased.
     *                       - 'price_per_gb' (float): The price per GB.
     * @return Order|null The created Order instance, or null on failure.
     */
    public function createNewOrder(array $data): ?Order
    {
        try {
            return Order::create([
                'user_id' => $data['user_id'],
                'total_cent_price' => $data['total_cent_price'],
                'status' => $data['status'] ?? 'pending',
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



    public function createOrderWithTransaction(User $user, array $fulfilledNumbers, int $actualTotalCostCents, int $maxPotentialCost, $servicesByCode)
    {
        return DB::transaction(function () use ($user, $fulfilledNumbers, $actualTotalCostCents, $maxPotentialCost, $servicesByCode) {

            // Lock the user row securely for the final math
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first();

            // 1. Silently refund the max amount we reserved earlier in the Facade
            $lockedUser->increment('balance_cents', $maxPotentialCost);

            $order = $this->createNewOrder([
                'user_id' => $lockedUser->id,
                'total_cent_price' => $actualTotalCostCents,
                'status' => 'completed',
            ]);

            // 2. Call your existing createDebit method to charge them the ACTUAL amount.
            // This will decrement their balance correctly AND create a beautiful, single WalletTransaction log.
            TransactionFacade::createDebit(
                $user,
                $actualTotalCostCents,
                "Payment for Order #{$order->id}",
                (string) $order->id
            );


            // 4. Create the Order Items
            $orderItems = [];
            foreach ($fulfilledNumbers as $item) {
                $orderItems[] = OrderItemFacade::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'external_order_id' => $item['phone_data']['request_id'],
                    'service_name' => $servicesByCode->get($item['service_code'])['name'],
                    'phone_number' => $item['phone_data']['number'],
                    'price_cents' => $item['price'],
                    'status' => 'pending',
                ]);
            }

            return [
                'order' => clone $order,
                'numbers' => $orderItems
            ];
        });
    }
}

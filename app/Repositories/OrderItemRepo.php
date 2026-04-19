<?php

namespace App\Repositories;

use App\Actions\CacheCounterAction;
use App\Models\OrderItem;
use App\Repositories\Facades\TransactionFacade;
use App\Repositories\Interfaces\OrderItemInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderItemRepo implements OrderItemInterface
{
    public function all(array $filters = [])
    {
        return OrderItem::when(
            $filters['user_id'] ?? null,
            fn($query, $user_id) => $query->where('user_id', $user_id)
        )->when(
            $filters['with_messages'] ?? null,
            fn($query) => $query->with('messages')
        )
            ->when(
                $filters['service_name'] ?? null,
                fn($query, $service_name) => $query->where('service_name', 'like', "%$service_name%")
            )->when(
                $filters['with_user'] ?? null,
                fn($query) => $query->with('user')
            )
            ->when(
                $filters['phone_number'] ?? null,
                fn($query, $phone_number) => $query->where('phone_number', 'like', "%$phone_number%")
            )
            ->when(
                $filters['status'] ?? null,
                fn($query, $status) => $query->where('status', $status)
            )->when(
                $filters['order_id'] ?? null,
                fn($query, $order_id) => $query->where('order_id', $order_id)
            )
            ->latest()->paginate(20);
    }
    public function find(int $id)
    {
        return OrderItem::find($id);
    }
    public function create(array $data)
    {
        return OrderItem::create([
            'user_id' => $data['user_id'],
            'order_id' => $data['order_id'],
            'external_order_id' => $data['external_order_id'],
            'service_name' => $data['service_name'],
            'phone_number' => $data['phone_number'],
            'price_cents' => $data['price_cents'],
        ]);
    }
    public function update(OrderItem $orderItem, array $data)
    {
        $allowed = collect($data)->only(['status', 'external_order_id', 'price_cents'])->toArray();
        if (empty($allowed)) {
            return false;
        }
        return $orderItem->update($allowed);
    }

    public function reuse(OrderItem $orderItem, array $data)
    {
        DB::beginTransaction();
        try {
            $newOrderItem = OrderItem::create(array_merge($orderItem->toArray(), [
                'status' => 'pending',
                'external_order_id' => $data['external_order_id'],
                'price_cents' => $data['price_cents']
            ]));
            // $orderItem->update([
            //     'status' => 'pending',
            //     'external_order_id' => $data['external_order_id'],
            //     'price_cents' => $data['price_cents']
            // ]);

            TransactionFacade::createDebit(
                $orderItem->user,
                $data['price_cents'],
                "Payment for reused phone number #{$orderItem->phone_number}",
                $orderItem->order_id
            );

            CacheCounterAction::execute('pending_numbers_' . $orderItem->user_id, 1, 'increment');

            DB::commit();

            return $orderItem;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reuse phone number', [
                'error' => $e->getMessage(),
                'order_item_id' => $orderItem->id
            ]);

            throw $e;
        }
    }

    public function delete(OrderItem $orderItem)
    {
        try {
            $orderItem->update(['status' => 'cancelled']);
            return $orderItem;
        } catch (\Exception $e) {
            Log::error('Failed to delete phone number', [
                'error' => $e->getMessage(),
                'order_item_id' => $orderItem->id
            ]);

            throw $e;
        }
    }

    public function cancel(OrderItem $orderItem)
    {
        DB::beginTransaction();
        try {
            $orderItem->update(['status' => 'cancelled']);

            TransactionFacade::createCredit(
                $orderItem->user,
                $orderItem->price_cents,
                "Refund for cancelled phone number #{$orderItem->phone_number}",
                $orderItem->order_id
            );

            CacheCounterAction::execute('pending_numbers_' . $orderItem->user_id, 1, 'decrement');

            DB::commit();

            return $orderItem;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel phone number', [
                'error' => $e->getMessage(),
                'order_item_id' => $orderItem->id
            ]);

            throw $e;
        }
    }

    public function countPendingNumbers(int $user_id)
    {
        return Cache::remember('pending_numbers_' . $user_id, 60 * 60, function () use ($user_id) {
            return OrderItem::where('user_id', $user_id)->where('status', 'pending')->count();
        });
    }


    public function sumPhonesMonthly(?Carbon $from, ?Carbon $to, ?int $user_id)
    {
        $fromStr = $from ? $from->toDateString() : 'null';
        $toStr   = $to ? $to->toDateString() : 'null';
        $userStr = $user_id ?? 'null';
        $cacheKey = "sum.phones_{$fromStr}_{$toStr}_{$userStr}";

        return Cache::remember($cacheKey, 3600, function () use ($from, $to, $user_id) {
            $stats = DB::table('order_items')
                ->when($from, fn($query) => $query->whereDate('created_at', '>=', $from->toDateString()))
                ->when($to, fn($query) => $query->whereDate('created_at', '<=', $to->toDateString()))
                ->when($user_id, fn($query) => $query->where('user_id', $user_id))
                ->selectRaw("
                SUM(price_cents) as total_amount,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
                SUM(CASE WHEN status = 'timeout_refunded' THEN 1 ELSE 0 END) as refunded_count,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count
            ")
                ->first();

            return [
                'total_amount'     => (int) ($stats->total_amount ?? 0),
                'completed_count'  => (int) ($stats->completed_count ?? 0),
                'refunded_count'   => (int) ($stats->refunded_count ?? 0),
                'cancelled_count'  => (int) ($stats->cancelled_count ?? 0),
            ];
        });
    }
}

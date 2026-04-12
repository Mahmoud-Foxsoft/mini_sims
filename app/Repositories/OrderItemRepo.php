<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\Facades\TransactionFacade;
use App\Repositories\Interfaces\OrderItemInterface;
use Illuminate\Support\Facades\DB;

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
            )
            ->latest()->paginate(20);
    }
    public function find(int $id) {
        return OrderItem::find($id);
    }
    public function create(array $data) {
        return OrderItem::create([
            'user_id' => $data['user_id'],
            'order_id' => $data['order_id'],
            'external_order_id' => $data['external_order_id'],
            'service_name' => $data['service_name'],
            'phone_number' => $data['phone_number'],
            'price_cents' => $data['price_cents'],
        ]);
    }
    public function update(OrderItem $orderItem, array $data) {
        $allowed = collect($data)->only(['status'])->toArray();
        if (empty($allowed)) {
            return false;
        }
        return $orderItem->update($allowed);
    }
    public function delete(OrderItem $orderItem) {
        return $orderItem->update(['status' => 'cancelled']);
    }

    public function cancel(OrderItem $orderItem) {
        DB::beginTransaction();
        $orderItem->update(['status' => 'cancelled']);
        TransactionFacade::createCredit($orderItem->user, $orderItem->price_cents, "Refund for cancelled phone number #{$orderItem->phone_number} in order #{$orderItem->order_id}");
        DB::commit();
        return $orderItem;
    }
}

<?php

namespace App\Repositories\Interfaces;

use App\Models\OrderItem;

interface OrderItemInterface
{
    public function all(array $filters = []);
    public function find(int $id);
    public function create(array $data);
    public function update(OrderItem $orderItem, array $data);
    public function delete(OrderItem $orderItem);
    public function cancel(OrderItem $orderItem);
}

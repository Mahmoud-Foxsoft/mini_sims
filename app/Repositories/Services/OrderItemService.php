<?php

namespace App\Repositories\Services;

use App\Models\OrderItem;
use App\Repositories\Interfaces\OrderItemInterface;

class OrderItemService
{

    public function __construct(protected OrderItemInterface $repo)
    {
        $this->repo = $repo;
    }

    public function all(array $filters = []) {
        return $this->repo->all($filters);
    }

    public function find($id) {
        return $this->repo->find($id);
    }

    public function create(array $data) {
        return $this->repo->create($data);
    }

    public function update(OrderItem $orderItem, array $data) {
        return $this->repo->update($orderItem, $data);
    }

    public function delete(OrderItem $orderItem) {
        return $this->repo->delete($orderItem);
    }
    public function cancel(OrderItem $orderItem) {
        return $this->repo->cancel($orderItem);
    }

    public function countPendingNumbers(int $user_id) {
        return $this->repo->countPendingNumbers($user_id);
    }
}

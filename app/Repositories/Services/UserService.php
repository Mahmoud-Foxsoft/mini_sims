<?php

namespace App\Repositories\Services;

use App\Models\User;
use App\Repositories\Interfaces\UserInterface;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(protected UserInterface $repo) {}

    public function find(int $id): ?User
    {
        return $this->repo->find($id);
    }
    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }
    public function filter(array $filters): LengthAwarePaginator
    {
        return $this->repo->filter($filters);
    }
    public function addBalance(User $user, float $amount): bool
    {
        return $this->repo->addBalance($user, $amount);
    }

    public function checkBalance(User $user, float $amount): bool
    {
        return $this->repo->checkBalance($user, $amount);
    }

    public function sumTotalUsersMonthly(?Carbon $from = null, ?Carbon $to = null): int
    {
        return $this->repo->sumTotalUsersMonthly($from, $to);
    }

    public function getForSelect(string $email, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->getForSelect($email, $perPage);
    }
}

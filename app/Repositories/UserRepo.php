<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Facades\PlanFacade;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Interfaces\UserInterface;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserRepo implements UserInterface
{

    public function __construct(protected User $model) {}


    public function find(int $id): ?User
    {
        return $this->model->find($id);
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->model->find($id);
        if (!$user) {
            return false;
        }
        return $user->update($data);
    }


    public function filter(array $filters): LengthAwarePaginator
    {

        $query = $this->model
            ->select('users.*')
            ->selectSub(function ($query) {
                $query->from('orders')
                    ->selectRaw('SUM(total_cent_price)');
            }, 'total_spent');

        $query->when(isset($filters['name']), function ($query) use ($filters) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        })
            ->when(isset($filters['email']), function ($query) use ($filters) {
                $query->where('email', 'like', '%' . $filters['email'] . '%');
            })
            ->when(isset($filters['is_blocked']), function ($query) use ($filters) {
                if ($filters['is_blocked'] === 'true' || $filters['is_blocked'] === true) {
                    $query->where('is_blocked', true);
                }

                if ($filters['is_blocked'] === 'false' || $filters['is_blocked'] === false) {
                    $query->where('is_blocked', false);
                }
            })->when(
                (!empty($filters['sort'])),
                function ($query) use ($filters) {
                    foreach ($filters['sort'] as $field => $direction) {
                        $query->orderBy($field, $direction);
                    }
                },
            );

        return $query->paginate(20);
    }

    public function addBalance(User $user, float $amount): bool
    {
        try {
            $user->increment('balance_cents', $amount);
            return true;
        } catch (\Throwable $th) {
            Log::error('Error adding balance', [$th->getMessage()]);
            return false;
        }
    }

    public function checkBalance(User $user, float $amount): bool
    {
        return $user->balance_cents / 100 >= $amount;
    }

    public function sumTotalUsersMonthly(?Carbon $from, ?Carbon $to): int
    {
        return Cache::remember('sum.users_' . $from?->toDateString() . '_' . $to?->toDateString(), 3600, function () use ($from, $to) {
            return User::query()
                ->where('email_verified_at', '!=', null)
                ->when($from && $to, fn($q) => $q->whereBetween('created_at', [$from, $to]))
                ->count();
        });
    }

    public function getForSelect(string $email, int $perPage = 10): LengthAwarePaginator
    {
        $users = $this->model
            ->select('id', 'name', 'email')
            ->when($email !== '', function ($query) use ($email) {
                $query->where(function ($innerQuery) use ($email) {
                    $innerQuery
                        ->where('email', 'like', '%' . $email . '%')
                        ->orWhere('name', 'like', '%' . $email . '%');
                });
            })
            ->paginate($perPage);

        return $users;
    }
}

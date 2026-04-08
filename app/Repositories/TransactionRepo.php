<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Repositories\Interfaces\TransactionInterface;

class TransactionRepo implements TransactionInterface
{
    public function __construct(protected WalletTransaction $model) {}

    public function getPaginated(array $filters = [], int $perPage = 20): array
    {
        $query = $this->model
            ->when(isset($filters['with_user']) && $filters['with_user'] !== null && $filters['with_user'] !== '', function ($query) {
                $query->with('user');
            })
            ->when(isset($filters['user_id']) && $filters['user_id'] !== null && $filters['user_id'] !== '', function ($query) use ($filters) {
                $query->where('user_id', $filters['user_id']);
            })
            ->when(isset($filters['email']) && $filters['email'] !== null && $filters['email'] !== '', function ($query) use ($filters) {
                $query->whereHas('user', function ($userQuery) use ($filters) {
                    $userQuery->where('email', 'like', '%'.$filters['email'].'%');
                });
            })
            ->when(isset($filters['type']) && $filters['type'] !== null && $filters['type'] !== '', function ($query) use ($filters) {
                $query->where('type', $filters['type']);
            })
            ->when(isset($filters['reference']) && $filters['reference'] !== null && $filters['reference'] !== '', function ($query) use ($filters) {
                $query->where('reference_id', 'like', '%'.$filters['reference'].'%');
            });

        $sums = $this->model->selectRaw('
    SUM(CASE WHEN type = ? THEN amount_cents ELSE 0 END) as credit_sum_cents,
    SUM(CASE WHEN type = ? THEN amount_cents ELSE 0 END) as debit_sum_cents
', ['credit', 'debit'])->first();

        $paginator = $query
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $result['transactions'] = $paginator;
        $result['credit_sum_cents'] = $sums['credit_sum_cents'];
        $result['debit_sum_cents'] = $sums['debit_sum_cents'];

        return $result;
    }

    public function createCredit(User $user, int $amountCents, string $description, ?string $reference = null): WalletTransaction
    {
        $transaction = $this->model->create([
            'user_id' => $user->id,
            'type' => WalletTransaction::TYPE_CREDIT,
            'amount_cents' => $amountCents,
            'description' => $description,
            'reference_id' => $reference,
        ]);
        $user->increment('balance_cents', $amountCents);

        return $transaction;
    }

    public function createDebit(User $user, int $amountCents, string $description, ?string $reference = null): WalletTransaction
    {
        $transaction = $this->model->create([
            'user_id' => $user->id,
            'type' => WalletTransaction::TYPE_DEBIT,
            'amount_cents' => $amountCents,
            'description' => $description,
            'reference_id' => $reference,
        ]);
        $user->decrement('balance_cents', $amountCents);

        return $transaction;
    }

    public function getTransactionByReference(string $reference): ?WalletTransaction
    {
        return $this->model->where('reference_id', $reference)->first();
    }

    public function findById(int $id): ?WalletTransaction
    {
        return $this->model->with('user')->find($id);
    }

    public function updatePayment(WalletTransaction $transaction, array $data): bool
    {
        $allowed = collect($data)->only(['source', 'description', 'reference'])->toArray();
        if (empty($allowed)) {
            return false;
        }

        return $transaction->update($allowed);
    }
}

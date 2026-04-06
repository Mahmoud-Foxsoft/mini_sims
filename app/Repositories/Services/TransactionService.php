<?php

namespace App\Repositories\Services;

use App\Models\WalletTransaction;
use App\Repositories\Interfaces\TransactionInterface;

class TransactionService
{
    public function __construct(protected TransactionInterface $repo)
    {
    }

    public function getPaginated(array $filters = [], int $perPage = 20)
    {
        return $this->repo->getPaginated($filters, $perPage);
    }

    public function createCredit(
        \App\Models\User $user,
        int $amountCents,
        string $description,
        ?string $reference = null,
    ): WalletTransaction {
        return $this->repo->createCredit($user, $amountCents,$description, $reference);
    }

    public function createDebit(
        \App\Models\User $user,
        int $amountCents,
        string $description,
        ?string $reference = null,
    ): WalletTransaction {
        return $this->repo->createDebit($user, $amountCents,$description, $reference);
    }

    public function getTransactionByReference(string $reference) : ?WalletTransaction {
        return $this->repo->getTransactionByReference($reference);
    }

    public function findById(int $id): ?WalletTransaction
    {
        return $this->repo->findById($id);
    }
}

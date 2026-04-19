<?php

namespace App\Repositories\Interfaces;

use App\Models\WalletTransaction;

interface TransactionInterface
{
    public function getPaginated(array $filters = []): array;

    public function findById(int $id): ?WalletTransaction;

    public function getTransactionByReference(string $reference): ?WalletTransaction;

    public function createCredit(
        \App\Models\User $user,
        int $amountCents,
        string $description,
        ?string $reference = null,
    ): WalletTransaction;
    public function createDebit(
        \App\Models\User $user,
        int $amountCents,
        string $description,
        ?string $reference = null,
    ): WalletTransaction;
}

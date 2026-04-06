<?php

namespace App\Repositories\Facades;

use App\Repositories\Services\TransactionService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getPaginated(array $filters = [], int $perPage = 20)
 * @method static \App\Models\Transaction createCredit(\App\Models\User $user, int $amountCents,string $description, ?string $reference = null)
 * @method static \App\Models\Transaction createDebit(\App\Models\User $user, int $amountCents,string $description, ?string $reference = null)
 * @method static \App\Models\Transaction|null findById(int $id)
 * @method static \App\Models\Transaction|null getTransactionByReference(string $reference)
 *
 * @see \App\Repositories\Services\TransactionService
 */
class TransactionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TransactionService::class;
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWalletTransactionRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Repositories\Facades\TransactionFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Get paginated list of user's transactions.
     */
    public function index(Request $request)
    {
        $filters = $request->input('filters', []);
        $filters['with_user'] = true;
        $transactions = TransactionFacade::getPaginated(
            $filters
        );

        return $this->sendResponse($transactions, 'Transactions fetched successfully');
    }

    public function store(StoreWalletTransactionRequest $request)
    {
        $payload = $request->validated();

        try {
            $transaction = DB::transaction(function () use ($payload) {
                $user = User::query()->lockForUpdate()->find($payload['user_id']);

                if (! $user) {
                    return null;
                }

                if (
                    $payload['type'] === WalletTransaction::TYPE_DEBIT
                    && $payload['amount_cents'] > $user->balance_cents
                ) {
                    throw new \RuntimeException('Insufficient user balance for this debit.');
                }

                if ($payload['type'] === WalletTransaction::TYPE_CREDIT) {
                    return TransactionFacade::createCredit(
                        $user,
                        $payload['amount_cents'],
                        $payload['description'],
                        $payload['reference_id'] ?? null,
                    );
                }

                return TransactionFacade::createDebit(
                    $user,
                    $payload['amount_cents'],
                    $payload['description'],
                    $payload['reference_id'] ?? null,
                );
            });
        } catch (\RuntimeException $exception) {
            return $this->sendError($exception->getMessage(), [], 422);
        }

        if (! $transaction) {
            return $this->sendError('User not found', [], 404);
        }

        return $this->sendResponse($transaction->load('user'), 'Transaction created successfully');
    }
}

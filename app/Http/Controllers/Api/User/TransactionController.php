<?php

namespace App\Http\Controllers\Api\User;

use App\DTOs\ChargeRequestDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTransactionRequest;
use App\Repositories\Facades\TransactionFacade;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Get paginated list of user's transactions.
     */
    public function index(Request $request)
    {
        $filters = $request->input('filters', []);
        $filters['per_page'] = $request->input('per_page', 20);

        $transactions = TransactionFacade::getByUser(
            $request->user()->id,
            $filters
        );

        return $this->sendResponse($transactions, 'Transactions fetched successfully');
    }

    /**
     * Get user's current balance.
     */
    public function balance(Request $request)
    {
        $balanceCents = TransactionFacade::getBalance($request->user()->id);
        $balanceDollars = TransactionFacade::getBalanceInDollars($request->user()->id);

        return $this->sendResponse([
            'balance_cents' => $balanceCents,
            'balance' => $balanceDollars,
            'currency' => 'USD',
        ], 'Balance fetched successfully');
    }

    /**
     * Initiate a payment to add funds.
     */
    public function store(CreateTransactionRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $request->user();

            $dto = new ChargeRequestDto(
                user: $user,
                amountCents: $validated['amount_cents'],
                currency: $validated['currency'] ?? 'USD',
                paymentMethodId: $validated['payment_method_id'] ?? null,
                cryptoCurrency: $validated['source'] === 'crypto' ? $validated['currency'] ?? null : null,
                returnUrl: $validated['return_url'] ?? null,
                promoCode: $validated['promo_code'] ?? null,
                meta: $validated['meta'] ?? null,
            );

            $result = TransactionFacade::chargeAndCredit($dto, $validated['source']);

            if (! $result->success) {
                return $this->sendError($result->errorMessage ?? 'Payment failed', [], 400);
            }

            return $this->sendResponse([
                'transaction' => $result->toArray(),
                'status' => $result->success ? 'completed' : 'pending',
                'pay_address' => $result->payAddress,
                'pay_amount' => $result->payAmount,
                'redirect_url' => $result->redirectUrl,
            ], 'Payment initiated successfully');
        } catch (\Throwable $th) {
            return $this->sendError('Failed to process payment.', ['error' => $th->getMessage()], 500);
        }
    }
}

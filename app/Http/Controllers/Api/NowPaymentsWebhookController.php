<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\PaymentUpdated;
use App\Models\Payment;
use App\Models\Transaction;
use App\Repositories\Facades\PaymentFacade;
use App\Repositories\Facades\TransactionFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NowPaymentsWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $transactionId = $request->input('order_id');
        $paidAmount = $request->input('outcome_amount');
        $status = $request->input('payment_status');

        Log::info('NowPayments webhook received', [
            'order_id' => $transactionId,
            'status' => $status,
            'paid_amount' => $paidAmount,
        ]);

        // Only process finished payments
        if ($status !== 'finished') {
            return response()->json(['message' => 'Payment not finished yet']);
        }

        // Find the payment by transaction_id
        $payment = PaymentFacade::getPaymentByTransactionId($transactionId);

        if (! $payment) {
            Log::warning('Payment not found for webhook', ['order_id' => $transactionId]);

            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Check if already credited to avoid duplicate transactions
        if ($payment->credited_transaction_id) {
            Log::info('Payment already credited', ['payment_id' => $payment->id]);

            return response()->json(['message' => 'Payment already processed']);
        }

        // Update payment status
        PaymentFacade::updatePayment($payment, [
            'paid_amount' => $paidAmount,
            'status' => Payment::FINISHED_STATUS,
        ]);

        // Create a credit transaction for the user
        try {
            $user = $payment->user;
            $amountCents = (int) round($payment->amount * 100);

            $transaction = TransactionFacade::createCredit(
                $user,
                $amountCents,
                Transaction::SOURCE_CRYPTO,
                "Payment #{$payment->id}",
                [
                    'payment_id' => $payment->id,
                    'crypto_currency' => $payment->currency,
                    'paid_amount' => $paidAmount,
                    'transaction_id' => $transactionId,
                ]
            );

            // Link the transaction to the payment
            PaymentFacade::updatePayment($payment, [
                'credited_transaction_id' => $transaction->id,
                'has_used' => true,
            ]);

            $payment->refresh();

            event(new PaymentUpdated(
                userId: $payment->user_id,
                paymentId: $payment->id,
                status: $payment->status,
                amount: (float) $payment->amount,
            ));

            Log::info('Payment credited successfully', [
                'payment_id' => $payment->id,
                'transaction_id' => $transaction->id,
                'amount' => $payment->amount,
            ]);

            return response()->json(['message' => 'Payment received and credited']);
        } catch (\Throwable $e) {
            Log::error('Failed to credit payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Failed to credit payment'], 500);
        }
    }
}

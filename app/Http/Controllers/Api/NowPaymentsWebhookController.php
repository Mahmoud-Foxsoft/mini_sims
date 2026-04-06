<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Facades\PaymentFacade;
use Illuminate\Http\Request;

class NowPaymentsWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $transaction_id = $request->input('order_id');
        $paid_amount = $request->input('outcome_amount');
        $status = $request->input('payment_status');
        $payment = PaymentFacade::getPaymentByTransactionId($transaction_id);
        if ($payment) {
            PaymentFacade::updatePayment($payment,[
                'paid_amount' => $paid_amount,
                'status' => $status,
            ]);
        }
        return response()->json(['message' => 'payment recieved']);
    }
}

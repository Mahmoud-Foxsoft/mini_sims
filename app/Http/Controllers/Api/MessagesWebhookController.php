<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Facades\PaymentFacade;
use Illuminate\Http\Request;

class MessagesWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // TODO Save the message
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

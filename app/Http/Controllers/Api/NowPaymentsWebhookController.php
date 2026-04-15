<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Repositories\Facades\PaymentFacade;
use Illuminate\Http\Request;

class NowPaymentsWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $transaction_id = $request->input('order_id');
        $paid_amount = $request->input('outcome_amount');
        $payment = PaymentFacade::getPaymentByTransactionId($transaction_id);
        if ($payment) {
            PaymentFacade::updatePayment($payment,[
                'paid_amount' => $paid_amount,
                'status' => Payment::FINISHED_STATUS,
            ]);
        }
        return response()->json(['message' => 'payment recieved']);
    }
}

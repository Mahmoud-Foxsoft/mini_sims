<?php

namespace App\Observers;

use App\Notifications\AdminPaymentNotification;
use App\Models\Payment;
use App\Notifications\UserPaymentNotification;
use App\Repositories\Facades\PaymentFacade;
use App\Repositories\Facades\TransactionFacade;
use App\Repositories\Facades\UserFacade;
use Illuminate\Support\Facades\Mail;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function created(Payment $payment)
    {
        if ($payment->status === Payment::FINISHED_STATUS) {
            TransactionFacade::createCredit($payment->user, $payment->amount * 100, 'Payment Successed', $payment->transaction_id);
        }
    }

    /**
     * Handle the Payment "updated" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function updated(Payment $payment)
    {
        if ($payment->status === Payment::FINISHED_STATUS && !$payment->has_used) {
            TransactionFacade::createCredit($payment->user, $payment->amount * 100, 'Payment Successed', $payment->transaction_id);
            PaymentFacade::updatePayment($payment, [
                'has_used' => true,
            ]);
            Mail::to(env('ADMIN_EMAIL'))->send(new AdminPaymentNotification($payment->amount));
            $user = $payment->user;
            Mail::to($user)->send(new UserPaymentNotification($payment->amount,$user));
        }
    }

    /**
     * Handle the Payment "deleted" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function deleted(Payment $payment)
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function restored(Payment $payment)
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     *
     * @param  \App\Models\Payment  $payment
     * @return void
     */
    public function forceDeleted(Payment $payment)
    {
        //
    }
}

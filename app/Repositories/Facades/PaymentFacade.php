<?php

namespace App\Repositories\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\Payment|null createPayment(array $data, bool $force = false)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator getAllUserPayments(int $user_id, array $filters)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator getAllPayments(array $filters)
 * @method static bool updatePayment(\App\Models\Payment $payment, array $data)
 * @method static bool deletePayment(\App\Models\Payment $payment)
 * @method static \App\Models\Payment|null getPaymentByTransactionId(string $transaction_id)
 * @method static float sumAmountMonthly(?int $user_id = null)
 * @method static Collection sumAmountWithUserCount(Carbon $from, Carbon $to)
 * @see \App\Repositories\Services\PaymentService
 */
class PaymentFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Repositories\Services\PaymentService';
    }
}

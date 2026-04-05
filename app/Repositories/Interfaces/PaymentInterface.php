<?php

namespace App\Repositories\Interfaces;

use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PaymentInterface
{
    public function createPayment(array $data, bool $force): ?Payment;
    public function getAllUserPayments(int $user_id, array $filters): LengthAwarePaginator;
    public function getAllPayments(array $filters): LengthAwarePaginator;
    public function deletePayment(Payment $payment): bool;
    public function updatePayment(Payment $payment, array $data): bool;
    public function sumAmountMonthly(?int $user_id = null): float;
    public function sumAmountWithUserCount(Carbon $from, Carbon $to): Collection;
}

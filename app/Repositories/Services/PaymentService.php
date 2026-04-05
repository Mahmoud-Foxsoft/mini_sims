<?php

namespace App\Repositories\Services;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaymentService
{
    public function __construct(private PaymentInterface $repo) {}
    public function createPayment(array $data, bool $force = false): ?Payment
    {
        return $this->repo->createPayment($data, $force);
    }
    public function getAllUserPayments(int $user_id, array $filters): LengthAwarePaginator
    {
        return $this->repo->getAllUserPayments($user_id, $filters);
    }
    public function getAllPayments(array $filters): LengthAwarePaginator
    {
        return $this->repo->getAllPayments($filters);
    }
    public function updatePayment(Payment $payment, array $data): bool
    {
        return $this->repo->updatePayment($payment, $data);
    }
    public function deletePayment(Payment $payment): bool
    {
        return $this->repo->deletePayment($payment);
    }
    public function getPaymentByTransactionId(string $transaction_id): ?Payment
    {
        return Payment::where('transaction_id', $transaction_id)->first();
    }
    public function sumAmountMonthly(?int $user_id = null): float
    {
        return $this->repo->sumAmountMonthly($user_id);
    }
    public function sumAmountWithUserCount(Carbon $from, Carbon $to): Collection
    {
        return $this->repo->sumAmountWithUserCount($from, $to);
    }
}

<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaymentRepo implements PaymentInterface
{
    /**
     * Create a new payment for a user.
     *
     * Business rules (when $force is false):
     * - If the user's last payment was created less than 15 minutes ago, creation is blocked.
     * - If the user's last 3 payments all have a "waiting" status, creation is blocked.
     *
     * @param  array  $data  The payment data to insert. Must include 'user_id'.
     * @param  bool  $force  If true, bypasses the time and status restrictions Usefull for Admin.
     * @return bool Returns true if the payment was created successfully, false otherwise.
     */
    public function createPayment(array $data, bool $force = false): ?Payment
    {
        try {
            if (! $force) {
                $waiting_payments = Payment::where('user_id', $data['user_id'])->whereNotIn('status', [Payment::REFUNDED_STATUS, Payment::FINISHED_STATUS])
                    ->where('created_at', '>=', now()->subMinutes(15))->count();
                if ($waiting_payments >= 3) {
                    return null;
                }
            }

            return Payment::create([
                'user_id' => $data['user_id'],
                'amount' => $data['amount'],
                'currency' => strtoupper($data['currency']),
                'status' => $data['status'] ?? Payment::WAITING_STATUS,
                'transaction_id' => $data['transaction_id'],
                'paid_amount' => $data['paid_amount'],
            ]);
        } catch (\Throwable $th) {
            Log::error('Db Create Error', [$th->getMessage()]);

            return null;
        }
    }

    /**
     * Retrieve all payments for a given user with optional filters.
     *
     * Supported filters:
     * - status        : (string) Filter by payment status.
     * - created_date  : (string|Carbon) Filter by exact creation date (YYYY-MM-DD).
     * - per_page      : (int) Number of results per page (default: 20).
     *
     * Results are ordered by latest created first.
     *
     * @param  int  $user_id  The ID of the user whose payments should be retrieved.
     * @param  array  $filters  Optional filters to apply to the query.
     */
    public function getAllUserPayments(int $user_id, array $filters): LengthAwarePaginator
    {
        $payments = Payment::where('user_id', $user_id)
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) => $query->where('status', $status)
            )->when(
                $filters['created_date'] ?? null,
                function ($query) use ($filters) {
                    $date = Carbon::parse($filters['created_date'])->format('Y-m-d');
                    $query->whereDate('created_at', $date);
                }
            )->latest();

        return $payments->paginate(20);
    }

    /**
     * Retrieve all payments across all users with optional filters.
     *
     * Supported filters:
     * - status        : (string) Filter by payment status.
     * - created_date  : (string|Carbon) Filter by exact creation date (YYYY-MM-DD).
     * - email         : (string) Filter by partial match on the related user's email.
     * - per_page      : (int) Number of results per page (default: 20).
     *
     * Results are eager-loaded with the related `user` and ordered by latest created first.
     *
     * @param  array  $filters  Optional filters to apply to the query.
     */
    public function getAllPayments(array $filters): LengthAwarePaginator
    {
        $payments = Payment::with('user')
            ->when(
                $filters['status'] ?? null,
                fn ($query, $status) => $query->where('status', $status)
            )->when(
                $filters['created_date'] ?? null,
                function ($query) use ($filters) {
                    $date = Carbon::parse($filters['created_at'])->format('Y-m-d');
                    $query->whereDate('created_at', $date);
                }
            )->when(
                $filters['email'] ?? null,
                fn ($query, $email) => $query->whereHas('user', fn ($q) => $q->where('email', 'like', "%$email%"))
            )->latest();

        return $payments->paginate(20);
    }

    /**
     * Delete a payment record.
     *
     * Attempts to locate the payment and delete it. If the payment does not exist
     * or an exception occurs during deletion, the method will log the error and
     * return false.
     *
     * @param  Payment  $payment  The payment to delete.
     * @return bool True if the payment was deleted successfully, false otherwise.
     */
    public function deletePayment(Payment $payment): bool
    {
        try {
            $payment->delete();

            return true;
        } catch (\Throwable $th) {
            Log::error('Error Deleting payment', [$th->getMessage()]);

            return false;
        }
    }

    /**
     * Update payment record.
     *
     * Only the allowed fields (`status`, `paid_amount`, `has_used`) will be updated,
     * and only if they exist in the provided data array.
     *
     * @param  Payment  $payment  The payment to update.
     * @param  array  $data  Key-value pairs of fields to update.
     * @return bool True if the payment was found and updated successfully, false otherwise.
     */
    public function updatePayment(Payment $payment, array $data): bool
    {
        try {
            $allowed = collect($data)->only(['status', 'paid_amount', 'has_used'])->toArray();
            if (empty($allowed)) {
                return false;
            }
            $payment->update($allowed);

            return true;
        } catch (\Throwable $th) {
            Log::error('Error updating payment', [$th->getMessage()]);

            return false;
        }
    }

    /**
     * Get the total finished payments for the current month, cached for 1 hour.
     *
     * This method calculates the sum of the `amount` field from the `payments` table
     * for the given user (if provided), filtered by:
     * - `status = FINISHED_STATUS`
     * - `created_at` within the current month.
     *
     * The result is cached for 1 hour to improve performance.
     *
     * @param  int|null  $user_id  The ID of the user to filter by, or null for all users.
     * @param  Carbon|null  $from  Optional start date to filter the payments.
     * @param  Carbon|null  $to  Optional end date to filter the payments.
     * @return float The total finished payment amount for the current month.
     */
    public function sumAmountMonthly(?int $user_id = null): float
    {
        return Cache::remember('sum.payment'.$user_id, 3600, function () use ($user_id) {
            return Payment::query()
                ->when($user_id, fn ($q) => $q->where('user_id', $user_id))
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->where('status', Payment::FINISHED_STATUS)
                ->sum('amount');
        });
    }

    public function sumAmountWithUserCount(Carbon $from, Carbon $to): Collection
    {
        return Cache::remember('sum.payment_with_user_count_'.$from->toDateString().'_'.$to->toDateString(), 3600, function () use ($from, $to) {
            return Payment::query()
                ->whereBetween('created_at', [$from, $to])
                ->where('status', Payment::FINISHED_STATUS)
                ->selectRaw('SUM(amount) as total_amount, COUNT(distinct user_id) as user_count')
                ->get();
        });
    }
}

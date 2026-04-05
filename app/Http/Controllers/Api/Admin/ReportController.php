<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display admin reports with optional date range filters.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'filters.from' => 'nullable|date',
            'filters.to' => 'nullable|date|after_or_equal:filters.from',
        ]);

        $from = data_get($validated, 'filters.from');
        $to = data_get($validated, 'filters.to');

        $fromDate = $from ? Carbon::parse($from)->startOfDay() : null;
        $toDate = $to ? Carbon::parse($to)->endOfDay() : null;

        $usersCount = $this->applyDateRange(User::query(), $fromDate, $toDate)->count();
        $subscriptionsCount = $this->applyDateRange(UserSubscription::query(), $fromDate, $toDate)->count();

        $finishedPaymentsQuery = Payment::query()->where('status', Payment::FINISHED_STATUS);
        $finishedPaymentsQuery = $this->applyDateRange($finishedPaymentsQuery, $fromDate, $toDate);
        $finishedPaymentsCount = (clone $finishedPaymentsQuery)->count();
        $finishedPaymentsUsd = (float) (clone $finishedPaymentsQuery)->sum('amount');

        $creditTransactionsCents = (int) $this->applyDateRange(
            Transaction::query()->where('type', Transaction::TYPE_CREDIT),
            $fromDate,
            $toDate
        )->sum('amount_cents');

        $debitTransactionsCents = (int) $this->applyDateRange(
            Transaction::query()->where('type', Transaction::TYPE_DEBIT),
            $fromDate,
            $toDate
        )->sum('amount_cents');

        $creditTransactionsUsd = $creditTransactionsCents / 100;
        $debitTransactionsUsd = $debitTransactionsCents / 100;
        $netTransactionsUsd = $creditTransactionsUsd - $debitTransactionsUsd;

        $totals = [
            ['key' => 'Users Joined', 'value' => (string) $usersCount, 'icon' => 'fa-thin fa-users', 'route' => 'users'],
            ['key' => 'Subscriptions Created', 'value' => (string) $subscriptionsCount, 'icon' => 'fa-thin fa-credit-card', 'route' => 'subscriptions'],
            ['key' => 'Finished Payments', 'value' => (string) $finishedPaymentsCount, 'icon' => 'fa-thin fa-money-bill', 'route' => 'payments'],
            ['key' => 'Payments Revenue (USD)', 'value' => number_format($finishedPaymentsUsd, 2), 'icon' => 'fa-thin fa-dollar-sign', 'route' => 'payments'],
            ['key' => 'Credit Transactions (USD)', 'value' => number_format($creditTransactionsUsd, 2), 'icon' => 'fa-thin fa-arrow-trend-up', 'route' => 'transactions'],
            ['key' => 'Net Transactions (USD)', 'value' => number_format($netTransactionsUsd, 2), 'icon' => 'fa-thin fa-chart-line', 'route' => 'transactions'],
        ];

        return $this->sendResponse([
            'filters' => [
                'from' => $fromDate?->toDateString(),
                'to' => $toDate?->toDateString(),
            ],
            'totals' => $totals,
        ], 'Reports retrieved successfully');
    }

    /**
     * Apply created_at date range to a query builder.
     */
    private function applyDateRange(Builder $query, ?Carbon $fromDate, ?Carbon $toDate): Builder
    {
        if ($fromDate) {
            $query->where('created_at', '>=', $fromDate);
        }

        if ($toDate) {
            $query->where('created_at', '<=', $toDate);
        }

        return $query;
    }
}

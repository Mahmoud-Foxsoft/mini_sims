<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\DTOs\TotalObject;
use App\Repositories\Facades\OrderFacade;
use App\Repositories\Facades\OrderItemFacade;
use App\Repositories\Facades\PaymentFacade;
use App\Repositories\Facades\UserFacade;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $filters = (array) $request->input('filters');
        $from = isset($filters['from'])
            ? Carbon::parse($filters['from'])->startOfDay()
            : now()->startOfMonth();
        $to = isset($filters['to'])
            ? Carbon::parse($filters['to'])->endOfDay()
            : now()->endOfMonth();
        $orderTotals = OrderFacade::sumOrdersMonthly($from, $to, null) / 1000;
        $totalPayments = PaymentFacade::sumAmountWithUserCount($from, $to)->first();
        $totalUsers = UserFacade::sumTotalUsersMonthly($from, $to) . '';
        $phones = OrderItemFacade::sumPhonesMonthly($from, $to, null);
        $totals = [
            new TotalObject('New User this Period', $totalUsers, 'users'),
            new TotalObject('Total Payments For Period', $totalPayments['total_amount'] ?? 0, 'payments'),
            new TotalObject('Total Users Who Made Payments', $totalPayments['user_count'] ?? 0, 'payments'),
            new TotalObject('Total Orders Income For Period', $orderTotals . ' USD', 'orders'),
            new TotalObject('Total Completed Phones For Period', (string) $phones['completed_count'], 'phone-numbers'),
            new TotalObject('Total Refunded Phones For Period', (string) $phones['refunded_count'], 'phone-numbers'),
            new TotalObject('Total Cancelled Phones For Period', (string) $phones['cancelled_count'], 'phone-numbers'),
        ];
        return $this->sendResponse(['totals' => $totals], 'Admin Report fetched successfully');
    }
}

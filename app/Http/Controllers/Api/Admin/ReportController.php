<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\DTOs\TotalObject;
use App\Repositories\Facades\OrderFacade;
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
        $from = Carbon::parse($filters['from']);
        $to = Carbon::parse($filters['to']);
        $orderTotals = OrderFacade::sumOrdersMonthly($from, $to, null, null);
        $totalPayments = PaymentFacade::sumAmountWithUserCount($from, $to)->first();
        $totalUsers = UserFacade::sumTotalUsersMonthly($from, $to) . '';
        $totals = [
            new TotalObject('New User this Period', $totalUsers, 'users'),
            new TotalObject('Total Payments For Period', $totalPayments['total_amount'] ?? 0, 'payments'),
            new TotalObject('Total Users Who Made Payments', $totalPayments['user_count'], 'payments'),
            new TotalObject('Total Orders Income For Period', $orderTotals . ' USD', 'orders'),
        ];
        return $this->sendResponse(['totals' => $totals], 'Admin Report fetched successfully');
    }
}

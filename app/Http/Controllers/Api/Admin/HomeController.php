<?php

namespace App\Http\Controllers\Api\Admin;

use App\Repositories\Facades\UserFacade;
use App\Http\Controllers\Controller;
use App\Http\DTOs\TotalObject;
use App\Repositories\Facades\OrderFacade;
use App\Repositories\Facades\OrderItemFacade;
use App\Repositories\Facades\PaymentFacade;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $totalPayments = round(PaymentFacade::sumAmountMonthly(), 2) . ' USD';
        $totalUsers = UserFacade::sumTotalUsersMonthly() . '';
        $orders = round(OrderFacade::sumOrdersMonthly(now()->startOfMonth(), now()->endOfMonth()) / 1000, 2) . ' USD';
        $phones = OrderItemFacade::sumPhonesMonthly(now()->startOfMonth(), now()->endOfMonth(), null);
        $totals = [
            new TotalObject('New User this Month', $totalUsers, 'users'),
            new TotalObject('Total Payments For Month', $totalPayments, 'payments'),
            new TotalObject('Total Orders For Month', $orders, 'orders'),
            new TotalObject('Total Completed Phones For Month', (string) $phones['completed_count'], 'phone-numbers'),
            new TotalObject('Total Refunded Phones For Month', (string) $phones['refunded_count'], 'phone-numbers'),
            new TotalObject('Total Cancelled Phones For Month', (string) $phones['cancelled_count'], 'phone-numbers'),
        ];
        return $this->sendResponse(['totals' => $totals], 'Admin Home fetched successfully');
    }
}

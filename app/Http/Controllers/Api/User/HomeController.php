<?php

namespace App\Http\Controllers\Api\User;

use App\Repositories\Facades\UserFacade;
use App\Http\Controllers\Controller;
use App\Http\DTOs\TotalObject;
use App\Models\EmailAccount;
use App\Models\Plan;
use App\Models\UsageCounter;
use App\Repositories\Facades\OrderFacade;
use App\Repositories\Facades\PaymentFacade;
use App\Repositories\Facades\PlanFacade;
use App\Repositories\Facades\PlanUsageFacade;
use App\Repositories\Facades\UserPlanFacade;
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
        $id = $request->user()->id;
        $totalPayments = round(PaymentFacade::sumAmountMonthly($id), 2) . ' USD';
        $orders = round(OrderFacade::sumOrdersMonthly(now()->startOfMonth(),now()->endOfMonth(),$id)->first()->total_amount, 2) . ' USD';
        $totals = [
            new TotalObject('Total Payments For Month', $totalPayments, 'payments'),
            new TotalObject('Total Orders For Month', $orders, 'orders'),
        ];
        return $this->sendResponse(['totals' => $totals], 'User home fetched successfully');
    }
}

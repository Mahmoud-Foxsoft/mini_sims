<?php

namespace App\Http\Controllers\Api\Admin;

use App\Repositories\Facades\UserFacade;
use App\Http\Controllers\Controller;
use App\Http\DTOs\TotalObject;
use App\Proxies\Facades\Proxy;
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
        $totalPayments = round(PaymentFacade::sumAmountMonthly(), 2) . ' USD';
        $totalUsers = UserFacade::sumTotalUsersMonthly() . '';
        $userPlansTotal = UserPlanFacade::sumTotalUserPlansMonthly();
        $totalUserPlanLimits = $userPlansTotal->sum('sum_data_limit');
        $totalOwedGbs = $userPlansTotal->sum('sum_owed_gbs') .' GB';
        $totals = [
            new TotalObject('New User this Month', $totalUsers, 'users'),
            new TotalObject('Total Payments For Month', $totalPayments, 'payments'),
            new TotalObject('New Data Limit this Month', $totalUserPlanLimits, 'user-plans'),
            new TotalObject('Total Owed GB User Plans this Month', $totalOwedGbs, 'user-plans'),
        ];
        $plans = PlanFacade::getAllPlans([])->transform(function ($plan) use ($userPlansTotal) {
            $resellerInfo = Proxy::driver($plan->driver_code)->resellerInfo();
            $userPlanSum = $userPlansTotal->where('plan_id', $plan->id)->first();
            $plan->reseller_info  = $resellerInfo;
            $plan->user_plan_sum = $userPlanSum;
            return $plan;
        });
        return $this->sendResponse(['plans' => $plans, 'totals' => $totals], 'Admin Home fetched successfully');
    }
}

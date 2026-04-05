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
        $user = $request->user();
        $usageCount = UsageCounter::where('user_id', $user->id)->first();
        $totalEmployees = $user->employees()->count();
        $totalTeams = $user->teams()->count();
        $connectedEmailAccounts = $user->emailAccounts()->where('desired_state', EmailAccount::ACTIVE_STATE)->count();
        $totalEmailAccounts = $user->emailAccounts()->count();
        $totalPayments = round(PaymentFacade::sumAmountMonthly($user->id), 2) . ' USD';
        $plans = Plan::where('is_active', true)->get();
        $totals = [
            ['key' => 'Total Teams', 'value' => (string) $totalTeams, 'icon' => 'fa-users', 'route' => 'teams'],
            ['key' => 'Total Employees', 'value' => (string) $totalEmployees, 'icon' => 'fa-people-group', 'route' => 'employees'],
            ['key' => 'Total Email Accounts', 'value' => (string) $totalEmailAccounts, 'icon' => 'fa-envelope', 'route' => 'emails'],
            ['key' => 'Total Running Email Accounts', 'value' => (string) $connectedEmailAccounts, 'icon' => 'fa-server', 'route' => 'emails'],
            ['key' => 'Total Payments (This Month)', 'value' => $totalPayments, 'icon' => 'fa-credit-card', 'route' => 'payments'],
            ['key' => 'Remaining SMTP Quota', 'value' => (string) $usageCount ? $usageCount->remaining_smtp_forward : 0, 'icon' => 'fa-chart-line', 'route' => null],
        ];

        return $this->sendResponse(['userPlan' => $user->subscription, 'totals' => $totals, 'plans' => $plans], 'User home fetched successfully');
    }
}

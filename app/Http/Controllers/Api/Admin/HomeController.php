<?php

namespace App\Http\Controllers\Api\Admin;

use App\Repositories\Facades\UserFacade;
use App\Http\Controllers\Controller;
use App\Http\DTOs\TotalObject;
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
        $totals = [
            new TotalObject('New User this Month', $totalUsers, 'users'),
            new TotalObject('Total Payments For Month', $totalPayments, 'payments'),
        ];
        return $this->sendResponse(['totals' => $totals], 'Admin Home fetched successfully');
    }
}

<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\DTOs\TotalObject;
use App\Http\Services\PhoneServiceService;
use App\Repositories\Facades\OrderFacade;
use App\Repositories\Facades\PaymentFacade;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $services = PhoneServiceService::getPhoneServices((array) $request->input('filters', []));
        return $this->sendResponse([
            'services' => $services,
        ], 'Phone services retrieved successfully');
    }
}

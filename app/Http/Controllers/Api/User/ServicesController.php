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
        $filters = (array) $request->input('filters', []);
        $filters['search'] = $request->string('search')->trim()->toString();
        $perPage = min(max($request->integer('per_page', 20), 1), 100);

        return $this->sendResponse(
            PhoneServiceService::paginatePhoneServices($filters, $perPage),
            'Phone services retrieved successfully'
        );
    }
}

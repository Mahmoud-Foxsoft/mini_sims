<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Repositories\Facades\OrderFacade;
use Exception;
use Illuminate\Support\Facades\Cache;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $filters = (array) $request->input('filters', []);
        $filters['user_id'] = $request->user()->id;
        $orders = OrderFacade::getAllOrders($filters);
        return $this->sendResponse($orders, 'Orders retrieved successfully.');
    }

    public function store(OrderStoreRequest $request)
    {
        try {
            $result = OrderFacade::processOrder(
                $request->user(),
                $request->validated('service_code'),
                $request->validated('quantity')
            );
            return $this->sendResponse($result, 'Order created successfully.');
        } catch (Exception $e) {
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;

            return $this->sendError($e->getMessage(), [], $statusCode);
        }
    }
}

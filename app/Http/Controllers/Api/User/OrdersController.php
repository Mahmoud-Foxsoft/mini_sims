<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Repositories\Facades\OrderFacade;
use Exception;

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
                $request->validated('cart')
            );
            defer(function () use ($result) {
                event(new \App\Events\OrderPlaced($result['order']));
            });
            return $this->sendResponse($result, 'Order created successfully.');
        } catch (Exception $e) {
            // Determine the HTTP status code based on the exception code
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;

            return $this->sendError($e->getMessage(), [], $statusCode);
        }
    }
}

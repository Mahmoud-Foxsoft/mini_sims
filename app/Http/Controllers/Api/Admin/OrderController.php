<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Facades\OrderFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $filters = (array) $request->input('filters');
            $filters['with_user'] = true;
            $orders = OrderFacade::getAllOrders($filters);
            return $this->sendResponse($orders, 'Orders retrieved successfully.');
        } catch (\Throwable $th) {
            Log::error('Error fetching orders: ' . $th->getMessage(), ['stack' => $th->getTraceAsString()]);
            return $this->sendError('Failed to retrieve orders.', ['error' => $th->getMessage()], 500);
        }
    }
}

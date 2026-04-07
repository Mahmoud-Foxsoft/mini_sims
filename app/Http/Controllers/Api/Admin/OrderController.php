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
            $orders = OrderFacade::getAllOrders((array) $request->input('filters'));
            return $this->sendResponse(['orders' => $orders], 'Orders retrieved successfully.');
        } catch (\Throwable $th) {
            Log::error('Error fetching orders: ' . $th->getMessage(), ['stack' => $th->getTraceAsString()]);
            return $this->sendError('Failed to retrieve orders.', ['error' => $th->getMessage()], 500);
        }
    }
}

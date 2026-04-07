<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Facades\OrderItemFacade;

class OrderItemsController extends Controller
{
    public function index(Request $request)
    {
        $filters =(array) $request->input('filters', []);
        $filters['user_id'] = $request->user()->id;
        $filters['with_messages'] = true;
        $orderItems = OrderItemFacade::all($filters);
        return $this->sendResponse(['orderItems' => $orderItems], 'Orders retrieved successfully.');
    }
}

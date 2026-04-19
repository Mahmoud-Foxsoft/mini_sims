<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Repositories\Facades\OrderItemFacade;

class OrderItemsController extends Controller
{
    public function index(Request $request)
    {
        $filters = (array) $request->input('filters', []);
        $filters['with_messages'] = true;
        $orderItems = OrderItemFacade::all($filters);
        return $this->sendResponse($orderItems, 'Orders retrieved successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);
        $deletedCount = OrderItem::whereIn('id', $validated['ids'])
            ->where('user_id', $request->user()->id)
            ->where('status', '!=', 'pending')
            ->delete();
        return $this->sendResponse(null, "Successfully deleted {$deletedCount} phone number(s).");
    }
}

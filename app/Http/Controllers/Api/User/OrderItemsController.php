<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\PhoneNumberService;
use App\Http\Services\PhoneServiceService;
use App\Models\OrderItem;
use App\Repositories\Facades\OrderItemFacade;
use App\Repositories\Facades\UserFacade;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class OrderItemsController extends Controller
{
    public function index(Request $request)
    {
        $filters = (array) $request->input('filters', []);
        $filters['user_id'] = $request->user()->id;
        $filters['with_messages'] = true;
        $orderItems = OrderItemFacade::all($filters);
        return $this->sendResponse($orderItems, 'Orders retrieved successfully.');
    }

    public function cancel(Request $request, OrderItem $orderItem)
    {
        if (!$orderItem || $orderItem->order->user_id !== $request->user()->id) {
            return $this->sendError('Phone number not found or access denied.', [], 404);
        }

        if (!in_array($orderItem->status, ['pending'])) {
            return $this->sendError('Only pending phone numbers can be cancelled.', [], 400);
        }

        if (Carbon::parse($orderItem->created_at)->diffInMinutes(Carbon::now()) < 2) {
            return $this->sendError('Phone number cannot be cancelled within 2 minutes of creation.', [], 400);
        }

        $lock = Cache::lock('cancel_order_item_' . $orderItem->id, 10);

        if (!$lock->get()) {
            return $this->sendError('Cancellation is already in progress for this number.', [], 429);
        }

        try {
            $success = PhoneNumberService::cancelPhoneNumber($orderItem->external_order_id, $request->user()->id);
            // $success = true; // Simulate success for testing purposes
            if ($success) {
                OrderItemFacade::cancel($orderItem);
                return $this->sendResponse(null, 'Phone number cancelled successfully.');
            } else {
                return $this->sendError('Failed to cancel phone number. Check for messages.', [], 500);
            }
        } catch (\Exception $e) {
            return $this->sendError('Failed to cancel phone number: ' . $e->getMessage(), [], 500);
        } finally {
            // Always release the lock when finished
            $lock->release();
        }
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

    public function reuse(Request $request, OrderItem $orderItem)
    {
        if (!$orderItem || $orderItem->order->user_id !== $request->user()->id) {
            return $this->sendError('Phone number not found or access denied.', [], 404);
        }

        if (!in_array($orderItem->status, ['completed'])) {
            return $this->sendError('Only completed phone numbers can be reused.', [], 400);
        }
        $pendingUserPhones = OrderItemFacade::countPendingNumbers($request->user()->id);
        if (config('app.max_pending_numbers') - $pendingUserPhones <= 0) {
            return $this->sendError('You have reached the maximum number of pending phone numbers.', [], 400);
        }

        $service = PhoneServiceService::getPhoneServices(['name' => $orderItem->service_name]);
        $service = $service[0] ?? null;
        if (!$service || !UserFacade::checkBalance($request->user(), $service['price'])) {
            return $this->sendError('Insufficient balance to reuse phone number.', [], 400);
        }
        $lock = Cache::lock('reuse_order_item_' . $orderItem->id, 10);

        if (!$lock->get()) {
            return $this->sendError('Reuse is already in progress for this number.', [], 429);
        }
        try {
            $response = PhoneNumberService::reusePhoneNumber($orderItem->external_order_id, $request->user()->id);
            if ($response) {
                OrderItemFacade::reuse($orderItem, $response);
                return $this->sendResponse(null, 'Phone number reused successfully.');
            } else {
                return $this->sendError('Failed to reuse phone number. Check for messages.', [], 500);
            }
        } catch (\Exception $e) {
            $orderItem->update(['status' => 'exhausted']);
            return $this->sendError('Failed to reuse phone number', [], 500);
        } finally {
            $lock->release();
        }
    }
}

<?php

namespace App\Http\Controllers\Api\User;

use App\Proxies\Facades\Proxy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Facades\GbAllocationFacade;
use App\Repositories\Facades\OrderFacade;
use App\Repositories\Facades\UserFacade;
use App\Repositories\Facades\UserPlanFacade;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $filters =(array) $request->input('filters', []);
        $filters['user_id'] = $request->user()->id;
        $orders = OrderFacade::getAllOrders($filters);
        return $this->sendResponse($orders, 'Orders retrieved successfully.');
    }

    public function store(OrderStoreRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();

        try {
            // Validate user plan access
            $userPlan = UserPlanFacade::find($data['user_plan_id']);
            if (!$userPlan || $userPlan->user_id !== $user->id) {
                return $this->sendError('User plan not found or access denied.', [], 404);
            }

            $plan = $userPlan->plan;
            $totalCost = $data['gb_amount'] * $plan->price_per_gb;

            // Check user balance
            if (!UserFacade::checkBalance($user, $totalCost)) {
                return $this->sendError('Insufficient balance.', [], 403);
            }

            // Calculate GB allocation
            $allocation = GbAllocationFacade::getRemainingGb($data['gb_amount'], $plan, $userPlan->gb_limit);

            if (empty($allocation)) {
                return $this->sendError('Failed to allocate GB. please try again later', [], 200);
            }
            if ($allocation['added_gb'] === 0) {
                return $this->sendError('Failed to create Order. Please try again later');
            }
            // Update owed GB if necessary
            if ($data['gb_amount'] - $allocation['added_gb'] > 0) {
                UserPlanFacade::addOwedGbsToUserPlan($userPlan, $data['gb_amount'] - $allocation['added_gb']);
            }

            // Update user on TorchLabs
            if (!Proxy::driver($plan->driver_code)->updateUser($userPlan->provider_user_id, $allocation['added_gb'])) {
                Log::error('TorchLabs update failed', [
                    'user_id' => $user->id,
                    'user_plan_id' => $userPlan->id,
                    'gb_amount' => $allocation['added_gb']
                ]);
                return $this->sendError('Failed to create order.', [], 500);
            }

            Proxy::driver($plan->driver_code)->resellerInfo(true);
            // Create order
            $order = OrderFacade::createNewOrder([
                'user_plan_id' => $data['user_plan_id'],
                'gb_amount' => $data['gb_amount'],
                'price_per_gb' => $plan->price_per_gb,
            ]);

            return $this->sendResponse(['order' => $order], 'Order created successfully.');
        } catch (\Throwable $th) {
            Log::error('Order creation failed: ' . $th->getMessage(), [
                'user_id' => $user->id,
                'request_data' => $data,
                'trace' => $th->getTraceAsString()
            ]);
            return $this->sendError('An error occurred while processing your request.', [], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\User;

use App\Actions\CacheCounterAction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Repositories\Facades\OrderFacade;
use Exception;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
    $serviceCode = $request->validated('service_code');
    $quantity = $request->validated('quantity');
    $user = $request->user();

    // 1. Lock ONLY the service to protect your provider from being overwhelmed.
    // It will auto-release after 10 seconds if something goes wrong.
    $serviceLock = Cache::lock('order_service_' . $serviceCode, 10);

    try {
        // Wait up to 5 seconds to get the lock
        $serviceLock->block(5);

        // Send to Facade
        $result = OrderFacade::processOrder($user, $serviceCode, $quantity);

        // Update pending numbers cache
        CacheCounterAction::execute('pending_numbers_' . $user->id, $result['items_count'], 'increment');

        return $this->sendResponse($result, 'Order created successfully.');

    } catch (LockTimeoutException $e) {
        return $this->sendError('Service is currently experiencing high demand. Please try again in a moment.', [], 429);
    } catch (Exception $e) {
        $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
        return $this->sendError($e->getMessage(), [], $statusCode);
    } finally {
        // Always release the lock so the next user can order
        optional($serviceLock)->release();
    }
}
}

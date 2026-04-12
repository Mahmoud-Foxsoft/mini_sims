<?php

namespace App\Http\Controllers\Api\User;

use App\Proxies\Facades\Proxy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Facades\GbAllocationFacade;
use App\Http\Services\PhoneNumberService;
use App\Http\Services\PhoneServiceService;
use App\Repositories\Facades\OrderFacade;
use App\Repositories\Facades\TransactionFacade;
use App\Repositories\Facades\UserFacade;
use App\Repositories\Facades\UserPlanFacade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $user = $request->user();
        $cartItems = $request->validated('cart');

        $maxPotentialCostCents = 0;
        $cartWithPrices = [];
        $services = PhoneServiceService::getPhoneServices();
        $servicesByCode = collect($services)->keyBy('code');
        foreach ($cartItems as $item) {
            $service = $servicesByCode->get($item['service_code']) ?? null;

            if (!$service) {
                return $this->sendError("Service with code {$item['service_code']} not found", [], 404);
            }

            $maxPotentialCostCents += $service['price'] * $item['quantity'];

            $cartWithPrices[] = [
                'service_code' => $item['service_code'],
                'quantity' => $item['quantity'],
                'unit_price' => $service['price']
            ];
        }
        // --- STEP 2: Check User Balance ---
        if (!UserFacade::checkBalance($user, $maxPotentialCostCents)) {
            return $this->sendError('Insufficient balance to complete this order.', [], 402);
        }
dd($cartWithPrices, $maxPotentialCostCents);

        // --- STEP 3: Request numbers from Central Server ---
        $fulfilledNumbers = [];
        $actualTotalCostCents = 0;

        foreach ($cartWithPrices as $item) {
            $returnedData = PhoneNumberService::requestPhoneNumbers(
                $item['service_code'],
                $item['quantity'],
                $user->id
            );

            // Loop through what actually came back (e.g., requested 5, might only get 3)
            foreach ($returnedData as $phoneRecord) {
                $fulfilledNumbers[] = [
                    'service_code' => $item['service_code'],
                    'phone_data' => $phoneRecord,
                    'price_cents' => $item['unit_price_cents']
                ];
                $actualTotalCostCents += $item['unit_price_cents'];
            }
        }

        // If the server returned absolutely nothing, stop here
        if (empty($fulfilledNumbers)) {
            return $this->sendError('Failed to obtain any phone numbers from the provider. Please try again later.', [], 503);
        }

        // --- STEP 4: Save to Database & Debit Wallet safely ---
        DB::beginTransaction();
        try {
            // Create main order
            $order = OrderFacade::create([
                'user_id' => $user->id,
                'total_cents' => $actualTotalCostCents,
                'status' => 'completed',
            ]);

            // Create Order Items
            foreach ($fulfilledNumbers as $item) {
                OrderFacade::createItem($order->id, [
                    'service_code' => $item['service_code'],
                    // Adjust these keys based on the exact structure your central server returns
                    'phone_number' => $item['phone_data']['phone_number'] ?? null,
                    'request_id'   => $item['phone_data']['id'] ?? null,
                    'price_cents'  => $item['price_cents'],
                ]);
            }

            // Perform the exact debit using your TransactionFacade
            TransactionFacade::createDebit(
                $user,
                $actualTotalCostCents,
                "Payment for Order #{$order->id}",
                (string) $order->id
            );

            DB::commit();

            return $this->sendResponse([
                'order_id' => $order->id,
                'total_cents' => $actualTotalCostCents,
                'items_count' => count($fulfilledNumbers),
            ], 'Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed in database', ['error' => $e->getMessage()]);

            foreach ($fulfilledNumbers as $item) {
                if (isset($item['phone_data']['id'])) {
                    PhoneNumberService::cancelPhoneNumber($item['phone_data']['id'], $user->id);
                }
            }

            return $this->sendError('Failed to create order. All allocated phone numbers have been released. Please try again.', [], 500);
        }
    }
}

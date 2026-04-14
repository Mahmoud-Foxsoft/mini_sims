<?php

namespace App\Repositories\Services;

use App\Http\Services\PhoneNumberService;
use App\Http\Services\PhoneServiceService;
use App\Models\Order;
use App\Models\User;
use App\Models\UserPlan;
use App\Repositories\Facades\UserFacade;
use App\Repositories\Interfaces\OrderInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(private OrderInterface $repo) {}

    public function getAllOrders(array $filters): LengthAwarePaginator
    {
        return $this->repo->getAllOrders($filters);
    }

    public function createNewOrder(array $data): ?Order
    {
        return $this->repo->createNewOrder($data);
    }

    public function sumOrdersMonthly(?Carbon $from = null, ?Carbon $to = null, ?int $user_id = null)
    {
        return $this->repo->sumOrdersMonthly($from, $to, $user_id);
    }
    public function processOrder(User $user, array $cartItems): array
    {
        // --- STEP 1: Validate Services and Calculate Max Cost ---
        $maxPotentialCost = 0;
        $cartWithPrices = [];
        $services = PhoneServiceService::getPhoneServices();
        $servicesByCode = collect($services)->keyBy('code');

        foreach ($cartItems as $item) {
            $service = $servicesByCode->get($item['service_code']);

            if (!$service) {
                throw new Exception("Service with code {$item['service_code']} not found", 404);
            }

            $maxPotentialCost += $service['price'] * $item['quantity'];

            $cartWithPrices[] = [
                'service_code' => $item['service_code'],
                'quantity' => $item['quantity'],
                'unit_price' => $service['price']
            ];
        }

        // --- STEP 2: Check User Balance ---
        if (!UserFacade::checkBalance($user, $maxPotentialCost)) {
            throw new Exception('Insufficient balance to complete this order.', 402);
        }

        // --- STEP 3: Request numbers from Central Server ---
        $fulfilledNumbers = [];
        $actualTotalCostCents = 0;

        foreach ($cartWithPrices as $item) {
            $returnedData = PhoneNumberService::requestPhoneNumbers(
                $item['service_code'],
                $item['quantity'],
                $user->id
            );

            // Loop through what actually came back
            foreach ($returnedData as $phoneRecord) {
                $fulfilledNumbers[] = [
                    'service_code' => $item['service_code'],
                    'phone_data' => $phoneRecord,
                    'price_cents' => $item['unit_price']
                ];
                // Just add the unit price for each returned record
                $actualTotalCostCents += $item['unit_price'];
            }
        }

        if (empty($fulfilledNumbers)) {
            throw new Exception('Failed to obtain any phone numbers from the provider. Please try again later.', 503);
        }

        // --- STEP 4: Save to Database safely ---
        try {
            $order = $this->repo->createOrderWithTransaction(
                $user,
                $fulfilledNumbers,
                $actualTotalCostCents,
                $servicesByCode
            );

            return [
                'order' => $order,
                'total_cents' => $actualTotalCostCents,
                'items_count' => count($fulfilledNumbers),
            ];
        } catch (Exception $e) {
            Log::error('Order creation failed in database', ['error' => $e->getMessage()]);

            // Compensating transaction: Release external numbers if DB fails
            foreach ($fulfilledNumbers as $item) {
                if (isset($item['phone_data']['id'])) {
                    PhoneNumberService::cancelPhoneNumber($item['phone_data']['request_id'], $user->id);
                }
            }

            throw new Exception('Failed to create order. All allocated phone numbers have been released.', 500);
        }
    }
}

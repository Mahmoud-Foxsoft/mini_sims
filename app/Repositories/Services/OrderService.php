<?php

namespace App\Repositories\Services;

use App\Http\Services\PhoneNumberService;
use App\Http\Services\PhoneServiceService;
use App\Models\Order;
use App\Models\User;
use App\Models\UserPlan;
use App\Repositories\Facades\OrderItemFacade;
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
    public function processOrder(User $user, string $serviceCode, int $quantity): array
    {

        $baseMaxAmount = $user->max_pending_numbers ?? (int) config('app.max_pending_numbers');

        $amountUsedByUser = OrderItemFacade::countPendingNumbers($user->id);

        $availableSpace = max(0, $baseMaxAmount - $amountUsedByUser);

        if ($quantity > $availableSpace) {
            throw new Exception("Limit exceeded. You only have capacity for {$availableSpace} more numbers.", 422);
        }

        $services = PhoneServiceService::getPhoneServices();
        $servicesByCode = collect($services)->keyBy('code');

        $service = $servicesByCode->get($serviceCode);

        if (!$service) {
            throw new Exception("Service with code {$serviceCode} not found", 404);
        }

        $maxPotentialCost = $service['price'] * $quantity;

        // --- STEP 2: Check User Balance ---
        if (!UserFacade::checkBalance($user, $maxPotentialCost)) {
            throw new Exception('Insufficient balance to complete this order.', 402);
        }

        // --- STEP 3: Request numbers from Central Server ---
        $fulfilledNumbers = [];
        $actualTotalCostCents = 0;

        $returnedData = PhoneNumberService::requestPhoneNumbers(
            $serviceCode,
            $quantity,
            $user->id
        );

        // Loop through what actually came back
        foreach ($returnedData as $phoneRecord) {
            $fulfilledNumbers[] = [
                'service_code' => $serviceCode,
                'phone_data' => $phoneRecord,
                'price' => $service['price'],
            ];
            // Just add the unit price for each returned record
            $actualTotalCostCents += $service['price'] * 100;
        }

        if (empty($fulfilledNumbers)) {
            throw new Exception('Failed to obtain any phone numbers from the provider. Please try again later.', 503);
        }

        // --- STEP 4: Save to Database safely ---
        try {
            $response = $this->repo->createOrderWithTransaction(
                $user,
                $fulfilledNumbers,
                $actualTotalCostCents,
                $servicesByCode
            );

            return [
                'order' => $response['order'],
                'total_price' => $actualTotalCostCents / 100,
                'items_count' => count($fulfilledNumbers),
                'numbers' => $response['numbers'],
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

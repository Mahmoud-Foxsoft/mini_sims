<?php

namespace App\Repositories\Services;

use App\Http\Services\PhoneNumberService;
use App\Http\Services\PhoneServiceService;
use App\Models\Order;
use App\Models\User;
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
        // --- 1. CAPACITY & SERVICE CHECKS ---
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

        $maxPotentialCost = $service['price'] * 100 * $quantity;

        // --- 2. THE ATOMIC RESERVATION ---
        // This query says: "Only deduct the balance if they have enough money".
        // It happens instantly in the database, preventing race conditions.
        // Notice we do NOT create a WalletTransaction yet.
        $reserved = User::where('id', $user->id)
            ->where('balance_cents', '>=', $maxPotentialCost)
            ->decrement('balance_cents', $maxPotentialCost);

        if (!$reserved) {
            throw new Exception('Insufficient balance to complete this order.', 402);
        }

        $fulfilledNumbers = [];

        try {
            // --- 3. CALL EXTERNAL API ---
            $actualTotalCostCents = 0;

            $returnedData = PhoneNumberService::requestPhoneNumbers(
                $serviceCode,
                $quantity,
                $user->id
            );

            foreach ($returnedData as $phoneRecord) {
                $fulfilledNumbers[] = [
                    'service_code' => $serviceCode,
                    'phone_data' => $phoneRecord,
                    'price' => $service['price'] * 100,
                ];
                $actualTotalCostCents += $service['price'] * 100;
            }

            if (empty($fulfilledNumbers)) {
                throw new Exception('Failed to obtain any phone numbers from the provider.', 503);
            }

            // --- 4. FINALIZE IN REPOSITORY ---
            $response = $this->repo->createOrderWithTransaction(
                $user,
                $fulfilledNumbers,
                $actualTotalCostCents,
                $maxPotentialCost, // Passing this so the repo knows how much to refund
                $servicesByCode
            );

            return [
                'order' => $response['order'],
                'total_price' => $actualTotalCostCents / 100,
                'items_count' => count($fulfilledNumbers),
                'numbers' => $response['numbers'],
            ];
        } catch (Exception $e) {
            // --- 5. COMPENSATION (IF ANYTHING FAILED) ---
            // Put the silently reserved money back into their account
            User::where('id', $user->id)->increment('balance_cents', $maxPotentialCost);

            // Cancel any numbers that the provider actually gave us before the crash
            if (!empty($fulfilledNumbers)) {
                foreach ($fulfilledNumbers as $item) {
                    if (isset($item['phone_data']['request_id'])) {
                        PhoneNumberService::cancelPhoneNumber($item['phone_data']['request_id'], $user->id);
                    }
                }
            }

            Log::error('Order process failed', ['error' => $e->getMessage()]);
            throw new Exception($e->getMessage(), $e->getCode() ?: 500);
        }
    }
}

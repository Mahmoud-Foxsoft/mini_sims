<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\EstimatePriceRequest;
use App\Http\Services\NowPaymentService;
use Illuminate\Support\Facades\Log;

class NowPaymentController extends Controller
{
    public function __construct(protected NowPaymentService $nowPaymentService) {}

    public function estimate(EstimatePriceRequest $request)
    {
        $data = $request->validated();
        try {
            $response = $this->nowPaymentService->getEstimatePrice(1.05 * $data['amount'], $data['currency']);

            return $this->sendResponse(['data' => $response], 'estimated successfully');
        } catch (\Throwable $th) {
            Log::error('Error in estimation: '.$th->getMessage(), [
                'stack' => $th->getTraceAsString(),
            ]);

            return $this->sendError('Failed to estimate.', ['error' => 'Error in estimation'], 500);
        }
    }

    public function getCurrencies()
    {
        try {
            $selectedCurrencies = $this->nowPaymentService->getCurrencies();

            return $this->sendResponse(['currencies' => $selectedCurrencies], 'currencies fetched successfully');
        } catch (\Throwable $th) {
            Log::error('Error fetching currencies: '.$th->getMessage(), [
                'stack' => $th->getTraceAsString(),
            ]);

            return $this->sendError('Failed to currencies.', ['error' => 'Error in fetching currencies'], 500);
        }
    }
}

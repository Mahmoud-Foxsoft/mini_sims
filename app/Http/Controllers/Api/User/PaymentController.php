<?php

namespace App\Http\Controllers\Api\User;

use App\Factories\PaymentMethodFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStorePaymentRequest;
use App\Http\Requests\EstimatePriceRequest;
use App\Http\Services\NowPaymentService;
use App\Models\Payment;
use App\Repositories\Facades\PaymentFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        protected NowPaymentService $nowPaymentService
    ) {}

    /**
     * Get paginated list of user's payments.
     */
    public function index(Request $request)
    {
        $filters = $request->input('filters', []);
        $filters['per_page'] = $request->input('per_page', 20);

        $payments = PaymentFacade::getAllUserPayments(
            $request->user()->id,
            $filters
        );

        return $this->sendResponse($payments, 'Payments fetched successfully');
    }

    /**
     * Store a newly created payment for the authenticated user.
     *
     * @param  \App\Http\Requests\UserStorePaymentRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserStorePaymentRequest $request)
    {
        try {
            $user = $request->user();
            $paymentData = array_merge($request->validated(), ['user_id' => $user->id]);
            $nowPaymentResp = $this->nowPaymentService->createPayment(
                $paymentData['amount'],
                $paymentData['currency'],
                $request->user()
            );
            if (!$nowPaymentResp['hasCreated']) {
                return $this->sendError('Failed to create payment.', [], 200);
            }
            $paymentData['transaction_id'] = $nowPaymentResp['transaction_id'];
            $payment = PaymentFacade::createPayment($paymentData, false);

            if ($payment) {
                $payment->pay_address = $nowPaymentResp['pay_address'];
                $payment->pay_amount = $nowPaymentResp['pay_amount'];
                return $this->sendResponse(['payment' => $payment], 'Payment created successfully.');
            }

            return $this->sendError('Failed to create payment. You may have reached the limit of pending payments or created a payment too recently.', [], 422);
        } catch (\Throwable $th) {
            Log::error('Error creating user payment: ' . $th->getMessage(), [
                'user_id' => $request->user()?->id,
                'stack' => $th->getTraceAsString()
            ]);
            return $this->sendError('Failed to create payment.', ['error' => $th->getMessage()], 500);
        }
    }

    public function estimate(EstimatePriceRequest $request)
    {
        $data = $request->validated();
        try {
            $fee = (int) config('services.nowPayments.fee', 1.05);
            $response = $this->nowPaymentService->getEstimatePrice($fee * $data['amount'], $data['currency']);
            return $this->sendResponse(['data' => $response], 'estimated successfully');
        } catch (\Throwable $th) {
            Log::error('Error in estimation: ' . $th->getMessage(), [
                'stack' => $th->getTraceAsString()
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
            Log::error('Error fetching currencies: ' . $th->getMessage(), [
                'stack' => $th->getTraceAsString()
            ]);
            return $this->sendError('Failed to currencies.', ['error' => 'Error in fetching currencies'], 500);
        }
    }
}

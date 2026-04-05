<?php

namespace App\Http\Controllers\Api\User;

use App\Factories\PaymentMethodFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePaymentRequest;
use App\Models\Payment;
use App\Repositories\Facades\PaymentFacade;
use App\Services\NowPaymentService;
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
     * Create a new payment request.
     */
    public function store(CreatePaymentRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $request->user();

            $paymentMethod = PaymentMethodFactory::create($validated['source']);
            if (! $paymentMethod) {
                return $this->sendError('Unsupported payment source', [], 400);
            }

            $dto = $paymentMethod->preparePaymentDto($user, $validated);
            $result = $paymentMethod->chargeNow($dto);

            if (! $result->success) {
                return $this->sendError($result->errorMessage ?? 'Payment failed', [], 400);
            }

            return $this->sendResponse([
                'transaction' => $result->toArray(),
                'status' => $result->isPending ? 'pending' : ($result->success ? 'completed' : 'failed'),
                'pay_address' => $result->payAddress,
                'pay_amount' => $result->payAmount,
                'redirect_url' => $result->redirectUrl,
            ], 'Payment initiated successfully');
        } catch (\Throwable $th) {
            Log::error('Failed to create payment: '.$th->getMessage(), [
                'user_id' => $request->user()->id,
                'stack' => $th->getTraceAsString(),
            ]);

            return $this->sendError('Failed to create payment.', ['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Create a crypto payment via NowPayments.
     */
    protected function createCryptoPayment($user, array $validated)
    {
        $amount = $validated['amount'];
        $currency = $validated['currency'];

        // Create payment with NowPayments
        $result = $this->nowPaymentService->createPayment(
            price_amount: $amount,
            pay_currency: $currency,
            user: $user
        );

        if (! $result['hasCreated']) {
            return $this->sendError('Failed to create crypto payment', [], 500);
        }

        // Create the payment record in our database
        $payment = PaymentFacade::createPayment([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => strtoupper($currency),
            'paid_amount' => $result['pay_amount'],
            'transaction_id' => $result['transaction_id'],
            'payment_method' => 'crypto',
            'status' => Payment::WAITING_STATUS,
            'pay_address' => $result['pay_address'],
        ]);

        return $this->sendResponse([
            'payment' => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'paid_amount' => $payment->paid_amount,
                'status' => $payment->status,
                'pay_address' => $result['pay_address'],
                'transaction_id' => $payment->transaction_id,
            ],
        ], 'Payment created successfully');
    }

    /**
     * Get a specific payment.
     */
    public function show(Request $request, Payment $payment)
    {
        // Ensure user owns this payment
        if ($payment->user_id !== $request->user()->id) {
            return $this->sendError('Payment not found', [], 404);
        }

        return $this->sendResponse($payment, 'Payment fetched successfully');
    }
}

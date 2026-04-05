<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = (array) $request->input('filters');
        $perPage = (int) $request->input('per_page', 20);

        $query = Payment::with('user')
            ->orderBy('created_at', 'desc');

        if (!empty($filters['email'])) {
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('email', 'like', '%' . $filters['email'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (!empty($filters['created_at'])) {
            $query->whereDate('created_at', $filters['created_at']);
        }

        $payments = $query->paginate($perPage);

        return $this->sendResponse($payments, 'Payments retrieved successfully');
    }

    /**
     * Display the specified payment.
     */
    public function show(int $id): JsonResponse
    {
        $payment = Payment::with('user')->find($id);

        if (!$payment) {
            return $this->sendError('Payment not found', [], 404);
        }

        return $this->sendResponse($payment, 'Payment retrieved successfully');
    }

    /**
     * Update the specified payment.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return $this->sendError('Payment not found', [], 404);
        }

        $validated = $request->validate([
            'has_used' => 'sometimes|boolean',
            'paid_amount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|string|in:waiting,confirming,sending,finished,refunded',
        ]);

        $payment->update($validated);

        return $this->sendResponse($payment->fresh(), 'Payment updated successfully');
    }

    /**
     * Get payment statistics for dashboard.
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total' => Payment::count(),
            'finished' => Payment::where('status', Payment::FINISHED_STATUS)->count(),
            'waiting' => Payment::where('status', Payment::WAITING_STATUS)->count(),
            'confirming' => Payment::where('status', Payment::CONFIRMING_STATUS)->count(),
            'sending' => Payment::where('status', Payment::SENDING_STATUS)->count(),
            'refunded' => Payment::where('status', Payment::REFUNDED_STATUS)->count(),
        ];

        return $this->sendResponse($stats, 'Statistics retrieved successfully');
    }
}

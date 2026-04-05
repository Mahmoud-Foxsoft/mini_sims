<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EventStripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Component\HttpFoundation\Response;

class StripeWebhookController extends Controller
{
    public function __construct(
        protected EventStripeService $eventStripeService
    ) {
    }

    /**
     * Handle incoming Stripe webhooks.
     */
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        // Verify webhook signature
        try {
            $event = Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: Invalid payload', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe webhook received', [
            'type' => $event->type,
            'event_id' => $event->id,
        ]);

        try {
            $result = $this->eventStripeService->handleEvent($event);

            $statusCode = $result['success'] ? 200 : 404;

            return response()->json(['message' => $result['message']], $statusCode);
        } catch (\Exception $e) {
            // info('error',['trace' => $e->getTraceAsString()]);
            Log::error('Stripe webhook handler failed', [
                'type' => $event->type,
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Webhook handler failed'], 500);
        }
    }
}

<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NowPaymentService
{
    const CACHE_KEY = 'now_payments_';
    /**
     * Create a new instance of the NowPaymentService.
     *
     * @return self  A new instance of the NowPaymentService.
     */
    public static function new(): NowPaymentService
    {
        return new self();
    }

    /**
     * Fetch the list of supported currencies from the NowPayments API.
     *
     * Makes an HTTP GET request to the NowPayments `/merchant/coins` endpoint.
     * The request will automatically retry up to 3 times with a 200ms delay
     * between attempts to handle transient failures. If the request fails
     * after all retries, the error will be logged and an empty collection
     * will be returned instead of throwing an exception.
     *
     * @return \Illuminate\Support\Collection  A collection of supported currencies,
     *                                         or an empty collection if the request fails.
     */
    public function getCurrencies(): Collection
    {
        try {
            $currenciesArray = Cache::rememberForever(self::CACHE_KEY . 'currencies', function () {
                return Http::nowPayments()
                    ->retry(3, 200)
                    ->get('/merchant/coins')
                    ->json('selectedCurrencies', []);
            });
            return collect($currenciesArray);
        } catch (\Throwable $th) {
            Log::error('Error fetching currencies from NowPayments', [
                'exception' => $th->getMessage(),
            ]);
            return collect();
        }
    }

    /**
     * Retrieve an estimated price conversion from the NowPayments API.
     *
     * Sends a request to the `/estimate` endpoint with the given amount and currencies.
     * The request will automatically retry up to 3 times with a 200ms delay
     * between attempts to handle transient failures. If the request fails
     * after all retries, the error will be logged and an empty JSON response
     * will be returned.
     *
     * @param  float   $amount        The amount to convert.
     * @param  string  $currency_to   The target currency code (e.g., "btc").
     * @param  string  $currency_from The source currency code (default: "usd").
     *
     * @return \Illuminate\Http\JsonResponse  The estimated price conversion result,
     *                                        or an empty JSON response on failure.
     */
    public function getEstimatePrice(float $amount, string $currency_to, string $currency_from = 'usd'): array
    {
        try {
            $response = Http::nowPayments()
                ->retry(3, 200)
                ->get('/estimate', [
                    'amount' => $amount,
                    'currency_from' => $currency_from,
                    'currency_to' => $currency_to,
                ])->json();
            return $response;
        } catch (\Throwable $th) {
            Log::error('Error fetching estimate price from NowPayments', [
                'exception' => $th->getMessage(),
                'amount' => $amount,
                'currency_from' => $currency_from,
                'currency_to' => $currency_to,
            ]);
            return [];
        }
    }

    /**
     * Create a new payment request via the NowPayments API.
     *
     * Sends a POST request to the `/payment` endpoint with the provided amount,
     * pay currency, and user details. A unique transaction/order ID is generated
     * per user. The request will automatically retry up to 3 times with a 200ms
     * delay between attempts to handle transient failures. If the request fails
     * or does not return a `201 Created` response, the error will be logged.
     *
     * @param  float       $price_amount  The payment amount in USD.
     * @param  string      $pay_currency  The currency code the user will pay with (e.g., "btc").
     * @param  \App\Models\User  $user    The user initiating the payment.
     *
     * @return array{
     *     transaction_id: string,
     *     hasCreated: bool
     * }  Returns the generated transaction ID and a flag indicating
     *    whether the payment was successfully created.
     */
    public function createPayment(float $price_amount, string $pay_currency, User $user): array
    {
        $fee = (int) config('services.nowPayments.fee', 1.05);
        try {
            $response = Http::centralServer()
                ->retry(3, 200)
                ->post(config('services.centralServer.payment_url'), [
                    "amount" => $fee * $price_amount,
                    "currency" => $pay_currency,
                ])->json();
            $hasCreated = true;
        } catch (\Throwable $th) {
            Log::error('Error creating payment via NowPayments', [
                'exception' => $th->getMessage(),
                'user_id' => $user->id,
                'email' => $user->email,
                'amount' => $price_amount,
                'currency' => $pay_currency,
            ]);
            $hasCreated = false;
        }
        return [
            'pay_address' => $response['pay_address'] ?? null,
            'pay_amount' => $response['pay_amount'],
            'transaction_id' => $response['payment_request_id'] ?? null,
            'hasCreated' => $hasCreated,
        ];
    }
}

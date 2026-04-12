<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhoneNumberService
{
    public static function requestPhoneNumbers(string $service_code, int $count, int $user_id): array
    {
        try {
            $response = Http::centralServer()
                ->retry(3, 200)
                ->post(config('services.centralServer.phone_numbers_url'), [
                    'service_code' => $service_code,
                    'count' => $count,
                    'user_id' => $user_id,
                ])
                ->json();
            return $response['data'];
        } catch (\Throwable $th) {
            Log::error('Error fetching phone numbers from Central Server', [
                'exception' => $th->getMessage(),
            ]);
            return [];
        }
    }

    public static function cancelPhoneNumber(string $request_id,int $user_id): bool
    {
        try {
            $response = Http::centralServer()
                ->retry(3, 200)
                ->post(config('services.centralServer.cancel_url'), [
                    'request_id' => $request_id,
                    'user_id' => $user_id,
                ])
                ->json();
            return $response['success'] ?? false;
        } catch (\Throwable $th) {
            Log::error('Error cancelling phone number on Central Server', [
                'exception' => $th->getMessage(),
            ]);
            return false;
        }
    }
}

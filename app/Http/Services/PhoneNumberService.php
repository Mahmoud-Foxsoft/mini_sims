<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhoneNumberService
{
    public static function requestPhoneNumbers(string $service_code, int $count): array
    {
        try {
            $response = Http::centralServer()
                ->retry(3, 200)
                ->post(config('services.centralServer.phone_numbers_url'), [
                    'service_code' => $service_code,
                    'count' => $count,
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
}

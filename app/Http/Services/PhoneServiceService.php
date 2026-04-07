<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhoneServiceService
{
    const CACHE_KEY = 'phone_services_';

    public static function getPhoneServices(): array
    {
        if (empty(Cache::get(self::CACHE_KEY . 'services', []))) {
            Cache::forget(self::CACHE_KEY . 'services');
        }
        return Cache::remember(self::CACHE_KEY . 'services', 3600, function () {
            try {
                $response = Http::centralServer()
                    ->retry(3, 200)
                    ->get(config('services.centralServer.phone_services_url'))
                    ->json();
                return $response['data'];
            } catch (\Throwable $th) {
                Log::error('Error fetching phone services from Central Server', [
                    'exception' => $th->getMessage(),
                ]);
                return [];
            }
        });
    }
}

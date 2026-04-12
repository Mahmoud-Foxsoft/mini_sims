<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhoneServiceService
{
    const CACHE_KEY = 'phone_services_';

    public static function getPhoneServices(array $filters = []): array
    {
        $allServices = Cache::remember(self::CACHE_KEY . 'services', 3600, function () {
            try {
                $response = Http::centralServer()
                    ->retry(3, 200)
                    ->get(config('services.centralServer.phone_services_url'))
                    ->json();

                $services = [];

                if (is_array($response)) {
                    foreach ($response as $service) {
                        $services[] = [
                            'name' => $service['name'] ?? '',
                            'code' => $service['code'] ?? '',
                            'price' => isset($service['price']) ? $service['price'] / 100 : 0, // Convert cents to dollars
                            'available' => ($service['available'] ?? 0) > 0
                        ];
                    }
                }
                return $services;
            } catch (\Throwable $th) {
                Log::error('Error fetching phone services from Central Server', [
                    'exception' => $th->getMessage(),
                ]);
                return [];
            }
        });

        if (empty($filters)) {
            return $allServices;
        }

        $filteredServices = collect($allServices)->filter(function ($service) use ($filters) {
            if (isset($filters['name'])) {
                if (!str_contains(strtolower($service['name']), strtolower($filters['name']))) {
                    return false;
                }
            }

            if (isset($filters['code']) && $service['code'] !== $filters['code']) {
                return false;
            }

            if (isset($filters['price']) && $service['price'] != $filters['price']) {
                return false;
            }

            if (isset($filters['available'])) {
                $isAvailable = filter_var($filters['available'], FILTER_VALIDATE_BOOLEAN);
                if ($service['available'] !== $isAvailable) {
                    return false;
                }
            }

            return true;
        });

        return $filteredServices->values()->toArray();
    }
}

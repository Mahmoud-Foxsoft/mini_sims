<?php

namespace App\Http\Services;

use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhoneServiceService
{
    const CACHE_KEY = 'phone_services';
    const ONE_TIME_PLAN_TYPE = 1;

    public static function getPhoneServices(array $filters = []): array
    {
        return self::query($filters)
            ->get()
            ->map(fn (Service $service) => self::transformService($service))
            ->toArray();
    }

    public static function paginatePhoneServices(array $filters = [], int $perPage = 20): array
    {
        $paginator = self::query($filters)->paginate($perPage);

        return [
            'services' => $paginator->getCollection()
                ->map(fn (Service $service) => self::transformService($service))
                ->values()
                ->toArray(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ];
    }

    private static function query(array $filters = []): Builder
    {
        return Service::query()
            ->when(! empty($filters['search']), function (Builder $query) use ($filters) {
                $search = trim((string) $filters['search']);

                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when(! empty($filters['name']), function (Builder $query) use ($filters) {
                $query->where('name', 'like', '%' . trim((string) $filters['name']) . '%');
            })
            ->when(! empty($filters['code']), function (Builder $query) use ($filters) {
                $query->where('code', (string) $filters['code']);
            })
            ->when(isset($filters['price']) && $filters['price'] !== '', function (Builder $query) use ($filters) {
                $query->where('price_cents', (int) round(((float) $filters['price']) * 100));
            })
            ->orderBy('name')
            ->orderBy('id');
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function transformService(Service $service): array
    {
        return [
            'name' => $service->name,
            'code' => $service->code,
            'price' => $service->price_cents / 100,
        ];
    }

    public static function transformServiceForAdmin(Service $service): array
    {
        return [
            'id' => $service->id,
            'name' => $service->name,
            'code' => $service->code,
            'price' => $service->price_cents / 100,
            'price_cents' => $service->price_cents,
            'created_at' => $service->created_at,
            'updated_at' => $service->updated_at,
        ];
    }

    public static function syncFromProvider(): array
    {
        try {
            $response = Http::centralServer()
                ->retry(3, 200)
                ->get(config('services.centralServer.phone_services_url'))
                ->throw()
                ->json();

            $services = self::extractOneTimePlanServices($response);

            if ($services->isEmpty()) {
                Log::error('FoxSims one-time plan sync returned no services.');

                return [
                    'success' => false,
                    'message' => 'No one-time services were found in the FoxSims plans response.',
                    'count' => 0,
                ];
            }

            foreach ($services as $service) {
                Service::updateOrCreate(
                    ['code' => $service['code']],
                    [
                        'name' => $service['name'],
                        'price_cents' => $service['price_cents'],
                    ]
                );
            }

            self::forgetCache();

            return [
                'success' => true,
                'message' => 'Services synced successfully.',
                'count' => $services->count(),
            ];
        } catch (\Throwable $th) {
            Log::error('Error syncing phone services from FoxSims', [
                'exception' => $th->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to sync services from FoxSims.',
                'count' => 0,
            ];
        }
    }

    public static function extractOneTimePlanServices(array $response): Collection
    {
        $plans = collect(data_get($response, 'data.plans', []));

        $oneTimePlan = $plans->first(function (array $plan) {
            return (int) ($plan['type'] ?? 0) === self::ONE_TIME_PLAN_TYPE;
        });

        if (! is_array($oneTimePlan)) {
            return collect();
        }

        return collect($oneTimePlan['services'] ?? [])
            ->map(function (array $service) {
                return [
                    'name' => (string) ($service['name'] ?? ''),
                    'code' => (string) ($service['service_code'] ?? ''),
                    'price_cents' => (int) ($service['amount_cents'] ?? 0),
                ];
            })
            ->filter(function (array $service) {
                return $service['name'] !== '' && $service['code'] !== '';
            })
            ->values();
    }
}

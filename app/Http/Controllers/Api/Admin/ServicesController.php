<?php

namespace App\Http\Controllers\Api\Admin;

use App\Events\ServicesUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Http\Services\PhoneServiceService;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = (array) $request->input('filters', []);

        $query = Service::query()->orderBy('name');

        if (! empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (! empty($filters['code'])) {
            $query->where('code', $filters['code']);
        }

        if (isset($filters['price']) && $filters['price'] !== '') {
            $query->where('price_cents', (int) round(((float) $filters['price']) * 100));
        }

        $services = $query->get()->map(fn (Service $service) => PhoneServiceService::transformServiceForAdmin($service))->toArray();

        return $this->sendResponse([
            'services' => $services,
        ], 'Phone services retrieved successfully');
    }

    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = Service::create($request->validated());

        PhoneServiceService::forgetCache();
        event(new ServicesUpdated());

        return $this->sendResponse([
            'service' => PhoneServiceService::transformServiceForAdmin($service),
        ], 'Service created successfully');
    }

    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service->update($request->validated());

        PhoneServiceService::forgetCache();
        event(new ServicesUpdated());

        return $this->sendResponse([
            'service' => PhoneServiceService::transformServiceForAdmin($service->fresh()),
        ], 'Service updated successfully');
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        PhoneServiceService::forgetCache();
        event(new ServicesUpdated());

        return $this->sendResponse(null, 'Service deleted successfully');
    }
}

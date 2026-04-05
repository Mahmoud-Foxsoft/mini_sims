<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class GeoController extends Controller
{
    private function loadGeo(): array
    {
        return Cache::remember('proxy_empire_geo', 60 * 60 * 6, function () {
            $path = storage_path('app/proxy_empire_geo.json');
            if (!File::exists($path)) {
                return [];
            }
            return json_decode(File::get($path), true) ?? [];
        });
    }

    public function countries(): JsonResponse
    {
        $geo = $this->loadGeo();
        $countries = array_values(array_map(
            fn($c) => ['code' => $c['code'], 'name' => $c['name']],
            $geo
        ));
        return response()->json($countries);
    }

    public function states(Request $request): JsonResponse
    {
        $country = $request->query('country');
        $geo = $this->loadGeo();
        if (!$country || !isset($geo[$country])) {
            return response()->json([]);
        }
        $states = array_values(array_map(
            fn($s) => ['code' => $s['code'], 'name' => $s['name']],
            $geo[$country]['states'] ?? []
        ));
        return response()->json($states);
    }

    public function cities(Request $request): JsonResponse
    {
        $country = $request->query('country');
        $state = $request->query('state');
        $geo = $this->loadGeo();
        if (!$country || !$state || !isset($geo[$country]['states'][$state])) {
            return response()->json([]);
        }
        $cities = array_values(array_map(
            fn($c) => ['code' => $c['code'], 'name' => $c['name']],
            $geo[$country]['states'][$state]['cities'] ?? []
        ));
        return response()->json($cities);
    }
}
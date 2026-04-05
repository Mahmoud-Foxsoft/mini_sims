<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUpdateSettingRequest;
use App\Models\Setting;
use App\Repositories\Facades\SettingFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Display a listing of all settings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $settings = SettingFacade::getAllSettings();
            return $this->sendResponse($settings, 'Settings retrieved successfully.');
        } catch (\Throwable $th) {
            Log::error('Error fetching settings: ' . $th->getMessage(), ['stack' => $th->getTraceAsString()]);
            return $this->sendError('Failed to retrieve settings.', ['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Update a setting value and/or title by key.
     *
     * @param  \App\Http\Requests\AdminUpdateSettingRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AdminUpdateSettingRequest $request, Setting $setting)
    {
        try {
            // Prepare data array from validated request
            $data = collect($request->validated())->except(['key'])->toArray();

            $success = SettingFacade::updateSetting($setting, $data);
            if ($success) {
                $updatedFields = array_keys($data);
                $message = 'Setting updated successfully and cache cleared.';
                if (count($updatedFields) > 1) {
                    $message .= ' Updated fields: ' . implode(', ', $updatedFields);
                }
                return $this->sendResponse(null, $message);
            }
            return $this->sendError('Failed to update setting. Setting key may not exist or no valid fields provided.');
        } catch (\Throwable $th) {
            Log::error('Error updating setting: ' . $th->getMessage(), [
                'key' => $request->key,
                'data' => $request->except('key'),
                'stack' => $th->getTraceAsString()
            ]);
            return $this->sendError('Failed to update setting.', ['error' => $th->getMessage()], 500);
        }
    }

    /**
     * Warm up the settings cache.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function warmCache()
    {
        try {
            SettingFacade::warmCache();
            return $this->sendResponse(null, 'Settings cache warmed successfully.');
        } catch (\Throwable $th) {
            Log::error('Error warming settings cache: ' . $th->getMessage(), ['stack' => $th->getTraceAsString()]);
            return $this->sendError('Failed to warm cache.', ['error' => $th->getMessage()], 500);
        }
    }
}

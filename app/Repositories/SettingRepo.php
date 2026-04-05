<?php

namespace App\Repositories;

use App\Models\Setting;
use App\Repositories\Interfaces\SettingInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingRepo implements SettingInterface
{
    private const CACHE_KEY = 'settings.all';
    
    /**
    * Retrieve all settings with caching.
    *
    * @return \Illuminate\Database\Eloquent\Collection  A collection of all settings.
    */
    public function getAllSettings(): Collection
    {
        return Setting::all();
    }
    
    public function getCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $settings = $this->getAllSettings();
            return $settings->pluck('value','key')->toArray();
        });
    }
    
    /**
    * Update a setting value and/or title by key and invalidate cache.
    *
    * This method updates setting value and/or title.
    * If the setting doesn't exist, it will not create a new one.
    * After successful update, it clears the cache to ensure fresh data.
    *
    * @param  Setting  $setting   The setting to update.
    * @param  array   $data  The data to update (value and/or title).
    *
    * @return bool  True if the setting was updated successfully, false otherwise.
    */
    public function updateSetting(Setting $setting, array $data): bool
    {
        try {
            // Filter only allowed fields
            $allowedFields = collect($data)->only(['value', 'title'])->toArray();
            
            if (empty($allowedFields)) {
                Log::warning("No valid fields provided for setting update", ['id' => $setting->id, 'data' => $data]);
                return false;
            }
            
            if ($setting->type === 'image' && isset($allowedFields['value']) && $allowedFields['value'] instanceof \Illuminate\Http\UploadedFile) {
                
                // Delete old image
                if (!empty($setting->value)) {
                    $old = Str::after($setting->value, '/web_assets/');
                    if ($old && Storage::disk('assets')->exists($old)) {
                        Storage::disk('assets')->delete($old);
                    }
                }
                
                $path = $allowedFields['value']->store('img/media', 'assets');
                if ($path === false) {
                    Log::warning("Error uploading image for setting update", ['id' => $setting->id, 'data' => $data]);
                    return false;
                }
                $allowedFields['value'] = '/web_assets/' . $path;
            }
            
            $setting->update($allowedFields);
            
            // Clear cache after successful update
            $this->clearCache();
            
            Log::info("Setting updated and cache cleared", [
                'updated_fields' => array_keys($allowedFields),
                'data' => $allowedFields
            ]);
            
            return true;
        } catch (\Throwable $th) {
            Log::error('Error updating setting', ['data' => $data, 'error' => $th->getMessage()]);
            return false;
        }
    }
    
    /**
    * Clear the settings cache.
    *
    * @return void
    */
    private function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
    
    /**
    * Get a single setting value by key with caching.
    *
    * @param  string  $key  The setting key to retrieve.
    * @param  mixed   $default  Default value if setting not found.
    *
    * @return mixed  The setting value or default.
    */
    public function getSettingValue(string $key, $default = null)
    {
        $settings = $this->getAllSettings();
        $setting = $settings->where('key', $key)->first();
        
        return $setting ? $setting->value : $default;
    }
}

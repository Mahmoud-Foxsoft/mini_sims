<?php

namespace App\Repositories\Services;

use App\Models\Setting;
use App\Repositories\Interfaces\SettingInterface;
use Illuminate\Database\Eloquent\Collection;

class SettingService
{
    public function __construct(private SettingInterface $repo)
    {
    }

    /**
     * Get all settings with caching.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllSettings(): Collection
    {
        return $this->repo->getAllSettings();
    }

    public function getCached() : array
    {
        return $this->repo->getCached();
    }

    /**
     * Update a setting and clear cache.
     *
     * @param  Setting  $setting
     * @param  array   $data
     * @return bool
     */
    public function updateSetting(Setting $setting, array $data): bool
    {
        return $this->repo->updateSetting($setting, $data);
    }

    /**
     * Get a single setting value by key.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function getSettingValue(string $key, $default = null)
    {
        return $this->repo->getSettingValue($key, $default);
    }

    /**
     * Warm up the settings cache.
     *
     * @return void
     */
    public function warmCache(): void
    {
        $this->getAllSettings();
    }
}

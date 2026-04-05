<?php

namespace App\Repositories\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection getAllSettings()
 * @method static array getCached()
 * @method static bool updateSetting(\App\Models\Setting $setting, array $data)
 * @method static mixed getSettingValue(string $key, mixed $default = null)
 * @method static void warmCache()
 * @see \App\Repositories\Services\SettingService
 */
class SettingFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'App\Repositories\Services\SettingService';
    }
}
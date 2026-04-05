<?php

namespace App\Repositories\Interfaces;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;

interface SettingInterface
{
    public function getAllSettings(): Collection;
    public function getCached(): array;
    public function updateSetting(Setting $setting, array $data): bool;
    public function getSettingValue(string $key, $default = null);
}

<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function get(string $key, $default = null)
    {
        return Cache::remember(
            $this->cacheKey($key),
            500,
            function () use ($key, $default) {

                [$group, $name] = $this->splitKey($key);

                $setting = Setting::where('group', $group)
                    ->where('key', $name)
                    ->first();

                return $setting?->value ?? $default;
            }
        );
    }

    public function set(string $key, $value, string $type = 'string'): void
    {
        [$group, $name] = $this->splitKey($key);

        Setting::updateOrCreate(
            [
                'group' => $group,
                'key'   => $name,
            ],
            [
                'value' => $value,
                'type'  => $type,
            ]
        );

        Cache::forget($this->cacheKey($key));
    }

    protected function splitKey(string $key): array
    {
        if (! str_contains($key, '.')) {
            throw new \InvalidArgumentException(
                'Setting key must be in format group.key'
            );
        }

        return explode('.', $key, 2);
    }

    protected function cacheKey(string $key): string
    {
        return "setting:$key";
    }
}

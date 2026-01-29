<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingsService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever(
            $this->cacheKey($key),
            fn () => $this->resolve($key, $default)
        );
    }

    protected function resolve(string $key, mixed $default): mixed
    {
        [$group, $name] = $this->splitKey($key);

        $setting = Setting::where('group', $group)
            ->where('key', $name)
            ->first();

        if ($setting) {
            return $this->castValue($setting);
        }

        return config("branding.$key", $default);
    }

    public function set(string $key, mixed $value, string $type = 'string'): void
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

        Log::debug('clear cache for ' . $this->cacheKey($key));
        Cache::forget($this->cacheKey($key));
    }

    public function resetGroup(string $group): void
    {
        Setting::where('group', $group)->delete();

        Cache::flush(); // oder gezielt, siehe unten
    }

    public function setMany(array $values, string $type = 'string'): void
    {
        foreach ($values as $key => $value) {

//            dump($key, $value, $type);
            $this->set($key, $value, $type);
        }
    }

    protected function castValue(Setting $setting): mixed
    {
        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json'    => json_decode($setting->value, true),
            default   => $setting->value,
        };
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

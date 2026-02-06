<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
    /**
     * Get logo path or return null to use component
     */
    public function getLogo(): ?string
    {
        $logo = $this->get('branding.logo');

        if ($logo && Storage::disk('public')->exists($logo)) {
            return Storage::disk('public')->url($logo);
        }

        return null;
    }

    /**
     * Get favicon path or return default
     */
    public function getFavicon(): string
    {
        $favicon = $this->get('branding.favicon');

        if ($favicon && Storage::disk('public')->exists($favicon)) {
            return Storage::disk('public')->url($favicon);
        }

        return asset('favicon.ico');
    }

    /**
     * Upload and set logo
     */
    public function uploadLogo(UploadedFile $file): string
    {
        // Altes Logo löschen
        $oldLogo = $this->get('branding.logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        $path = $file->store('branding/logos', 'public');
        $this->set('branding.logo', $path, 'string');

        return $path;
    }

    /**
     * Upload and set favicon
     */
    public function uploadFavicon(UploadedFile $file): string
    {
        $oldFavicon = $this->get('branding.favicon');
        if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
            Storage::disk('public')->delete($oldFavicon);
        }

        $path = $file->store('branding/favicons', 'public');
        $this->set('branding.favicon', $path, 'string');

        return $path;
    }

    /**
     * Reset logo to component
     */
    public function resetLogo(): void
    {
        $logo = $this->get('branding.logo');
        if ($logo && Storage::disk('public')->exists($logo)) {
            Storage::disk('public')->delete($logo);
        }

        Setting::where('group', 'branding')
            ->where('key', 'logo')
            ->delete();

        Cache::forget($this->cacheKey('branding.logo'));
    }

    /**
     * Reset favicon to default
     */
    public function resetFavicon(): void
    {
        $favicon = $this->get('branding.favicon');
        if ($favicon && Storage::disk('public')->exists($favicon)) {
            Storage::disk('public')->delete($favicon);
        }

        Setting::where('group', 'branding')
            ->where('key', 'favicon')
            ->delete();

        Cache::forget($this->cacheKey('branding.favicon'));
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

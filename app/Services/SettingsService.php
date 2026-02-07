<?php

namespace App\Services;

use App\Models\Setting;
use enshrined\svgSanitize\Sanitizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;

class SettingsService
{
    /**
     * Get a setting value
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever(
            $this->cacheKey($key),
            fn () => $this->resolve($key, $default)
        );
    }

    /**
     * Resolve setting from database or config
     */
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

    /**
     * Set a single setting
     */
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

    /**
     * Set multiple settings at once
     */
    public function setMany(array $values, string $type = 'string'): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $type);
        }
    }

    /**
     * Reset all settings in a group
     */
    public function resetGroup(string $group): void
    {
        Setting::where('group', $group)->delete();
        Cache::flush();
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
     * Get favicon information with type detection
     */
    public function getFaviconInfo(): array
    {
        $favicon = $this->get('branding.favicon');

        if ($favicon && Storage::disk('public')->exists($favicon)) {
            $url = Storage::disk('public')->url($favicon);
            $extension = pathinfo($favicon, PATHINFO_EXTENSION);

            return [
                'url' => $url,
                'type' => match($extension) {
                    'svg' => 'svg',
                    'ico' => 'ico',
                    'png' => 'png',
                    default => 'default'
                },
                'path' => $favicon,
            ];
        }

        // Fallback zu Standard-Favicons
        return [
            'url' => null,
            'type' => 'default',
            'path' => null,
        ];
    }

    /**
     * Get favicon URL for specific size
     */
    public function getFaviconUrl(string $size = 'default'): string
    {
        $info = $this->getFaviconInfo();

        if ($info['type'] === 'default') {
            // Fallback zu Vite Assets
            return match($size) {
                '96' => Vite::asset('resources/images/favicon-96x96.png'),
                '180' => Vite::asset('resources/images/apple-touch-icon.png'),
                '512' => Vite::asset('resources/images/web-app-manifest-512x512.png'),
                'svg' => Vite::asset('resources/images/logo_commu-core.svg'),
                'ico' => Vite::asset('resources/images/favicon.ico'),
                default => Vite::asset('resources/images/favicon.ico'),
            };
        }

        if ($info['type'] === 'svg') {
            return $info['url'];
        }

        if ($info['type'] === 'ico') {
            return $info['url'];
        }

        // PNG - check for generated sizes
        if ($info['type'] === 'png') {
            $sizeFiles = [
                '96' => 'branding/favicons/favicon-96x96.png',
                '180' => 'branding/favicons/apple-touch-icon.png',
                '512' => 'branding/favicons/web-app-manifest-512x512.png',
            ];

            if (isset($sizeFiles[$size]) && Storage::disk('public')->exists($sizeFiles[$size])) {
                return Storage::disk('public')->url($sizeFiles[$size]);
            }

            // Fallback zum Original
            return $info['url'];
        }

        return $info['url'];
    }

    /**
     * Upload and set logo with SVG sanitization
     */
    public function uploadLogo(UploadedFile $file): string
    {
        // Validiere MIME-Type
        $allowedMimes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'image/svg+xml'];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Unerlaubter Dateityp');
        }

        // SVG Sanitization
        if ($file->getMimeType() === 'image/svg+xml') {
            $content = file_get_contents($file->getRealPath());

            $sanitizer = new Sanitizer();
            $cleanSvg = $sanitizer->sanitize($content);

            if ($cleanSvg === false) {
                throw new \InvalidArgumentException('SVG-Datei enthält potenziell schädlichen Code und wurde abgelehnt');
            }

            // Altes Logo löschen
            $oldLogo = $this->get('branding.logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Speichere gesäuberte SVG
            $path = 'branding/logos/' . uniqid() . '.svg';
            Storage::disk('public')->put($path, $cleanSvg);

            $this->set('branding.logo', $path, 'string');

            return $path;
        }

        // Normale Bild-Uploads
        $oldLogo = $this->get('branding.logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        $path = $file->store('branding/logos', 'public');
        $this->set('branding.logo', $path, 'string');

        return $path;
    }

    /**
     * Upload and set favicon with SVG sanitization and size generation
     */
    public function uploadFavicon(UploadedFile $file): string
    {
        // Validiere MIME-Type
        $allowedMimes = ['image/png', 'image/x-icon', 'image/vnd.microsoft.icon', 'image/svg+xml'];

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            throw new \InvalidArgumentException('Unerlaubter Dateityp');
        }

        // SVG Sanitization
        if ($file->getMimeType() === 'image/svg+xml') {
            $content = file_get_contents($file->getRealPath());

            $sanitizer = new Sanitizer();
            $cleanSvg = $sanitizer->sanitize($content);

            if ($cleanSvg === false) {
                throw new \InvalidArgumentException('SVG-Datei enthält potenziell schädlichen Code und wurde abgelehnt');
            }

            // Alte Favicons löschen
            $this->deleteOldFavicons();

            // Speichere gesäuberte SVG
            $path = 'branding/favicons/favicon.svg';
            Storage::disk('public')->put($path, $cleanSvg);

            $this->set('branding.favicon', $path, 'string');
            $this->set('branding.favicon_type', 'svg', 'string');

            return $path;
        }

        // PNG Upload - generiere verschiedene Größen
        if ($file->getMimeType() === 'image/png') {
            $this->deleteOldFavicons();

            // Speichere Original
            $path = $file->store('branding/favicons', 'public');

            $this->set('branding.favicon', $path, 'string');
            $this->set('branding.favicon_type', 'png', 'string');

            // Optional: Verschiedene Größen generieren (wenn GD installiert ist)
            $this->generateFaviconSizes($path);

            return $path;
        }

        // ICO Upload
        $this->deleteOldFavicons();

        $path = $file->storeAs('branding/favicons', 'favicon.ico', 'public');
        $this->set('branding.favicon', $path, 'string');
        $this->set('branding.favicon_type', 'ico', 'string');

        return $path;
    }

    /**
     * Delete old favicons
     */
    protected function deleteOldFavicons(): void
    {
        $oldFavicon = $this->get('branding.favicon');
        if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
            Storage::disk('public')->delete($oldFavicon);
        }

        // Lösche generierte Größen
        $sizes = ['favicon-96x96.png', 'apple-touch-icon.png', 'web-app-manifest-512x512.png'];
        foreach ($sizes as $size) {
            $path = 'branding/favicons/' . $size;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Generate different favicon sizes from PNG
     */
    protected function generateFaviconSizes(string $originalPath): void
    {
        if (!extension_loaded('gd')) {
            return; // GD nicht verfügbar
        }

        $fullPath = Storage::disk('public')->path($originalPath);

        try {
            $sizes = [
                'favicon-96x96.png' => 96,
                'apple-touch-icon.png' => 180,
                'web-app-manifest-512x512.png' => 512,
            ];

            foreach ($sizes as $filename => $size) {
                $this->resizeImage($fullPath, $size, 'branding/favicons/' . $filename);
            }
        } catch (\Exception $e) {
            Log::warning('Favicon size generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Resize image to specific size
     */
    protected function resizeImage(string $sourcePath, int $size, string $targetPath): void
    {
        $source = imagecreatefrompng($sourcePath);
        $resized = imagecreatetruecolor($size, $size);

        // Transparenz erhalten
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        imagecopyresampled(
            $resized, $source,
            0, 0, 0, 0,
            $size, $size,
            imagesx($source), imagesy($source)
        );

        $fullTargetPath = Storage::disk('public')->path($targetPath);
        $dir = dirname($fullTargetPath);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        imagepng($resized, $fullTargetPath);

        imagedestroy($source);
        imagedestroy($resized);
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
        $this->deleteOldFavicons();

        Setting::where('group', 'branding')
            ->where('key', 'favicon')
            ->delete();

        Setting::where('group', 'branding')
            ->where('key', 'favicon_type')
            ->delete();

        Cache::forget($this->cacheKey('branding.favicon'));
        Cache::forget($this->cacheKey('branding.favicon_type'));
    }

    /**
     * Cast setting value based on type
     */
    protected function castValue(Setting $setting): mixed
    {
        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json'    => json_decode($setting->value, true),
            default   => $setting->value,
        };
    }

    /**
     * Split key into group and name
     */
    protected function splitKey(string $key): array
    {
        if (! str_contains($key, '.')) {
            throw new \InvalidArgumentException(
                'Setting key must be in format group.key'
            );
        }

        return explode('.', $key, 2);
    }

    /**
     * Generate cache key
     */
    protected function cacheKey(string $key): string
    {
        return "setting:$key";
    }
}
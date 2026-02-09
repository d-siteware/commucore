<?php

declare(strict_types=1);

use App\Services\SettingsService;

if (! function_exists('format_bytes')) {
    function format_bytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return app(SettingsService::class)->get($key, $default);
    }
}

if (! function_exists('logo_url')) {
    function logo_url(): string
    {
        return app(\App\Services\SettingsService::class)->getLogo()
            ?? Vite::asset('resources/images/logo_commu-core.svg');
    }
}

if (! function_exists('favicon_url')) {
    function favicon_url(): string
    {
        return app(\App\Services\SettingsService::class)->getFavicon();
    }
}

<?php

declare(strict_types=1);

use App\Services\SettingsService;

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

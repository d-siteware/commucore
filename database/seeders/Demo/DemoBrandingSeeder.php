<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Services\SettingsService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

final class DemoBrandingSeeder extends Seeder
{
    private const LOGO_PATH = 'branding/logos/demo.svg';

    public function run(): void
    {
        Storage::disk('public')->put(self::LOGO_PATH, <<<'SVG'
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 520 120" role="img" aria-labelledby="title">
              <title id="title">CommuCore Demo</title>
              <rect x="4" y="4" width="112" height="112" rx="28" fill="#0f766e"/>
              <path d="M83 38a34 34 0 1 0 0 44" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="13"/>
              <text x="142" y="78" fill="#134e4a" font-family="Arial, sans-serif" font-size="45" font-weight="700">CommuCore</text>
            </svg>
            SVG);

        app(SettingsService::class)->set('branding.logo', self::LOGO_PATH);

        app(SettingsService::class)->set('branding.title', 'CommuCore');
    }
}

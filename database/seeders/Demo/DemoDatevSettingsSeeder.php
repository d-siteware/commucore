<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Services\Accounting\DatevSettingsService;
use Illuminate\Database\Seeder;

final class DemoDatevSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(DatevSettingsService::class);
        $settings->setBeraterNr('9999999');
        $settings->setMandantNr('99999');
    }
}

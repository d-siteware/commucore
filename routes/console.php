<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('gdpr:pseudonymize-members')->monthly();
Schedule::command('gdpr:purge-event-subscriptions')->daily();
Schedule::command('gdpr:purge-unsubscribed-mailing-list')->daily();

if (config('app.is_demo')) {
    Schedule::command('commucore:demoseeder --force')
        ->everySixHours()
        ->withoutOverlapping()
        ->runInBackground();
}

Schedule::command('datev:clean-archives --days=30')->daily();
<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Services\OnboardingStatusService;

trait InvalidatesOnboardingStatus
{
    protected static function bootInvalidatesOnboardingStatus(): void
    {
        static::created(function (): void {
            app(OnboardingStatusService::class)->invalidate();
        });

        static::updated(function (): void {
            app(OnboardingStatusService::class)->invalidate();
        });

        static::deleted(function (): void {
            app(OnboardingStatusService::class)->invalidate();
        });
    }
}
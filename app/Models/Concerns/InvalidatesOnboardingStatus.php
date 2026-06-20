<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Services\OnboardingStatusService;

/**
 * Invalidates the onboarding status cache when the model is created,
 * updated or deleted. Intended for models that affect the onboarding checklist.
 */
trait InvalidatesOnboardingStatus
{
    public static function bootInvalidatesOnboardingStatus(): void
    {
        static::created(fn () => app(OnboardingStatusService::class)->invalidate());
        static::updated(fn () => app(OnboardingStatusService::class)->invalidate());
        static::deleted(fn () => app(OnboardingStatusService::class)->invalidate());
    }
}

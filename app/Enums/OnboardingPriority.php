<?php

declare(strict_types=1);

namespace App\Enums;

enum OnboardingPriority: string
{
    case Critical = 'critical'; // rot — blockiert Aktivitäten-Sektion
    case Important = 'important'; // amber — empfohlen, nicht blockierend

    public function label(): string
    {
        return match ($this) {
            self::Critical => __('onboarding.badge.red'),
            self::Important => __('onboarding.badge.amber'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Critical => 'red',
            self::Important => 'amber',
        };
    }

    public function badgeIcon(): string
    {
        return match ($this) {
            self::Critical => 'exclamation-triangle',
            self::Important => 'information-circle',
        };
    }

    /**
     * Icon, das zusätzlich zum leeren Status-Kreis bei offenen Punkten
     * dieser Priorität angezeigt wird. Nur Critical bekommt eine
     * Hervorhebung, Important bleibt visuell neutral.
     */
    public function indicatorIcon(): ?string
    {
        return match ($this) {
            self::Critical => 'exclamation-triangle',
            self::Important => null,
        };
    }
}

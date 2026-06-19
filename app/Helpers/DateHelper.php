<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Locale;
use App\Services\LocaleService;
use Carbon\Carbon;

final class DateHelper
{
    /**
     * Formats a Carbon date using the current application locale.
     *
     * Examples (de): 2026-06-19 → "19.06.2026"
     * Examples (hu): 2026-06-19 → "2026.06.19."
     * Examples (en): 2026-06-19 → "06/19/2026"
     */
    public static function formatDate(?Carbon $date): string
    {
        if ($date === null) {
            return '';
        }

        try {
            return app(LocaleService::class)->formatDate($date);
        } catch (\RuntimeException) {
            return $date->format('d.m.Y');
        }
    }

    /**
     * Formats a Carbon time using the current application locale.
     */
    public static function formatTime(?Carbon $date): string
    {
        if ($date === null) {
            return '';
        }

        try {
            return app(LocaleService::class)->formatTime($date);
        } catch (\RuntimeException) {
            return $date->format('H:i');
        }
    }

    /**
     * Formats a date and time using the current application locale.
     *
     * Examples (de): 2026-06-19 14:30 → "19.06.2026 14:30"
     */
    public static function formatDateTime(?Carbon $date): string
    {
        if ($date === null) {
            return '';
        }

        return self::formatDate($date).' '.self::formatTime($date);
    }

    /**
     * Formats a date using a specific Locale's format.
     */
    public static function formatDateForLocale(?Carbon $date, Locale $locale): string
    {
        if ($date === null) {
            return '';
        }

        return $locale->formatDate($date);
    }
}

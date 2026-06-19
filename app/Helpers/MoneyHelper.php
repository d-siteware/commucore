<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Locale;
use App\Services\LocaleService;

final class MoneyHelper
{
    // =========================================================================
    // Parsing (Input → Cents)
    // =========================================================================

    /**
     * Converts a formatted money string (German locale) or raw numeric value to cents.
     *
     * Handles German format: "5.000,00" → 500000
     * Also handles plain:    "100"      → 10000
     *
     * @param  string|int|float|null  $value
     */
    public static function toCents(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_string($value)) {
            // Remove thousand separators (.) and replace decimal comma with dot
            $normalized = str_replace('.', '', $value);
            $normalized = str_replace(',', '.', $normalized);

            return (int) round((float) $normalized * 100);
        }

        return (int) round((float) $value * 100);
    }

    /**
     * Parses a money string using a specific Locale's separators.
     *
     * Example (de: decimal=',', thousands='.'):  "5.000,00" → 500000
     * Example (en: decimal='.', thousands=','):  "5,000.00" → 500000
     */
    public static function toCentsForLocale(string $value, Locale $locale): ?int
    {
        if ($value === '') {
            return null;
        }

        $normalized = str_replace($locale->thousands_separator, '', $value);
        $normalized = str_replace($locale->decimal_separator, '.', $normalized);

        return (int) round((float) $normalized * 100);
    }

    // =========================================================================
    // Formatting (Cents → Output)
    // =========================================================================

    /**
     * Formats cents using the current application locale.
     *
     * Examples:
     *   500000, withSymbol=true  → "5.000,00 €"
     *   500000, withSymbol=false → "5.000,00"
     *   null                     → ""
     */
    public static function formatCents(?int $cents, string $currency = '€', bool $withSymbol = true): string
    {
        if ($cents === null) {
            return '';
        }

        try {
            $locale = app(LocaleService::class)->getCurrentLocale();
            $formatted = $locale->formatCents($cents);
        } catch (\RuntimeException) {
            $formatted = number_format($cents / 100, 2, ',', '.');
        }

        return $withSymbol ? "{$formatted} {$currency}" : $formatted;
    }

    /**
     * Formats cents using a specific Locale's separators.
     *
     * Example (de): 123456 → "1.234,56 €"
     * Example (en): 123456 → "1,234.56 €"
     */
    public static function formatCentsForLocale(?int $cents, Locale $locale, string $currency = '€', bool $withSymbol = true): string
    {
        if ($cents === null) {
            return '';
        }

        $formatted = $locale->formatCents($cents);

        return $withSymbol ? "{$formatted} {$currency}" : $formatted;
    }

    /**
     * Formats cents for use in form input fields (no currency symbol).
     * Matches the format expected by x-mask:dynamic="$money($input, ',', '.')".
     *
     * Examples:
     *   500000 → "5.000,00"
     *   null   → ""
     */
    public static function centsToFormInput(?int $cents): string
    {
        return self::formatCents($cents, withSymbol: false);
    }
}

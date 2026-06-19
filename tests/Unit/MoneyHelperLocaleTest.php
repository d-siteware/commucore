<?php

declare(strict_types=1);

use App\Helpers\MoneyHelper;
use App\Models\Locale;

// ──────────────────────────────────────────────────────────────────────────
// MoneyHelper::formatCentsForLocale
// ──────────────────────────────────────────────────────────────────────────

describe('MoneyHelper::formatCentsForLocale', function (): void {

    it('formats cents with de locale separators', function (): void {
        $locale = new Locale([
            'name' => 'de',
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'currency_symbol' => 'EUR',
            'currency_position' => 'after',
        ]);

        expect(MoneyHelper::formatCentsForLocale(500_000, $locale))
            ->toBe('5.000,00 EUR');
    });

    it('formats cents with hu locale separators', function (): void {
        $locale = new Locale([
            'name' => 'hu',
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'currency_symbol' => 'HUF',
            'currency_position' => 'after',
        ]);

        expect(MoneyHelper::formatCentsForLocale(500_000, $locale))
            ->toBe('5.000,00 HUF');
    });

    it('formats cents with en locale separators and before position', function (): void {
        $locale = new Locale([
            'name' => 'en',
            'decimal_separator' => '.',
            'thousands_separator' => ',',
            'currency_symbol' => 'USD',
            'currency_position' => 'before',
        ]);

        expect(MoneyHelper::formatCentsForLocale(500_000, $locale))
            ->toBe('USD 5,000.00');
    });

    it('returns empty string for null cents', function (): void {
        $locale = new Locale(['name' => 'de']);

        expect(MoneyHelper::formatCentsForLocale(null, $locale))->toBe('');
    });

    it('returns formatted without symbol when withSymbol is false', function (): void {
        $locale = new Locale([
            'name' => 'de',
            'decimal_separator' => ',',
            'thousands_separator' => '.',
            'currency_symbol' => 'EUR',
            'currency_position' => 'after',
        ]);

        expect(MoneyHelper::formatCentsForLocale(500_000, $locale, withSymbol: false))
            ->toBe('5.000,00');
    });

});

// ──────────────────────────────────────────────────────────────────────────
// MoneyHelper::toCentsForLocale
// ──────────────────────────────────────────────────────────────────────────

describe('MoneyHelper::toCentsForLocale', function (): void {

    it('parses de formatted string', function (): void {
        $locale = new Locale([
            'name' => 'de',
            'decimal_separator' => ',',
            'thousands_separator' => '.',
        ]);

        expect(MoneyHelper::toCentsForLocale('5.000,00', $locale))->toBe(500_000);
    });

    it('parses en formatted string', function (): void {
        $locale = new Locale([
            'name' => 'en',
            'decimal_separator' => '.',
            'thousands_separator' => ',',
        ]);

        expect(MoneyHelper::toCentsForLocale('5,000.00', $locale))->toBe(500_000);
    });

    it('returns null for empty string', function (): void {
        $locale = new Locale(['name' => 'de']);

        expect(MoneyHelper::toCentsForLocale('', $locale))->toBeNull();
    });

});

// ──────────────────────────────────────────────────────────────────────────
// MoneyHelper::getCurrencySymbol
// ──────────────────────────────────────────────────────────────────────────

describe('MoneyHelper::getCurrencySymbol', function (): void {

    it('returns EUR in unit test context (fallback)', function (): void {
        expect(MoneyHelper::getCurrencySymbol())->toBe('EUR');
    });

});

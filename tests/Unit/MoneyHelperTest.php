<?php

declare(strict_types=1);

use App\Helpers\MoneyHelper;

describe('MoneyHelper::toCents', function (): void {

    it('converts german formatted string to cents', function (): void {
        expect(MoneyHelper::toCents('5.000,00'))->toBe(500_000)
            ->and(MoneyHelper::toCents('1.234,56'))->toBe(123_456)
            ->and(MoneyHelper::toCents('100,00'))->toBe(10_000)
            ->and(MoneyHelper::toCents('100'))->toBe(10_000)
            ->and(MoneyHelper::toCents('0,50'))->toBe(50);
    });

    it('returns null for empty string', function (): void {
        expect(MoneyHelper::toCents(''))->toBeNull();
    });

    it('returns null for null', function (): void {
        expect(MoneyHelper::toCents(null))->toBeNull();
    });

    it('returns int as-is', function (): void {
        expect(MoneyHelper::toCents(123_456))->toBe(123_456);
    });

    it('converts float to cents', function (): void {
        expect(MoneyHelper::toCents(50.0))->toBe(5_000);
    });

});

describe('MoneyHelper::formatCents', function (): void {

    it('formats cents to german locale string with symbol', function (): void {
        expect(MoneyHelper::formatCents(500_000))->toBe('5.000,00 €')
            ->and(MoneyHelper::formatCents(123_456))->toBe('1.234,56 €')
            ->and(MoneyHelper::formatCents(50))->toBe('0,50 €');
    });

    it('formats cents without currency symbol', function (): void {
        expect(MoneyHelper::formatCents(500_000, withSymbol: false))->toBe('5.000,00');
    });

    it('returns empty string for null', function (): void {
        expect(MoneyHelper::formatCents(null))->toBe('');
    });

});

describe('MoneyHelper::centsToFormInput', function (): void {

    it('returns formatted string without symbol', function (): void {
        expect(MoneyHelper::centsToFormInput(500_000))->toBe('5.000,00')
            ->and(MoneyHelper::centsToFormInput(null))->toBe('');
    });

});

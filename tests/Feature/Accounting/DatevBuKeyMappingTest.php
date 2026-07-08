<?php

declare(strict_types=1);

use App\Services\Accounting\Datev\DatevBuKeyMapping;

describe('DatevBuKeyMapping', function (): void {

    it('returns null for 0% vat (steuerfrei)', function (): void {
        expect(DatevBuKeyMapping::fromVatPercent(0))->toBeNull()
            ->and(DatevBuKeyMapping::fromVatPercent(0, isExpense: true))->toBeNull();
    });

    it('returns USt keys for income (Einnahmen)', function (): void {
        expect(DatevBuKeyMapping::fromVatPercent(7))->toBe('2')
            ->and(DatevBuKeyMapping::fromVatPercent(19))->toBe('3');
    });

    it('returns Vorsteuer keys for expenses (Ausgaben)', function (): void {
        expect(DatevBuKeyMapping::fromVatPercent(7, isExpense: true))->toBe('8')
            ->and(DatevBuKeyMapping::fromVatPercent(19, isExpense: true))->toBe('9');
    });

    it('returns null for unknown vat rates', function (): void {
        expect(DatevBuKeyMapping::fromVatPercent(5))->toBeNull()
            ->and(DatevBuKeyMapping::fromVatPercent(16))->toBeNull()
            ->and(DatevBuKeyMapping::fromVatPercent(16, isExpense: true))->toBeNull();
    });

    it('returns empty string for toCsvValue with 0% vat', function (): void {
        expect(DatevBuKeyMapping::toCsvValue(0))->toBe('');
    });

    it('returns BU-Schlüssel string for toCsvValue', function (): void {
        expect(DatevBuKeyMapping::toCsvValue(7))->toBe('2')
            ->and(DatevBuKeyMapping::toCsvValue(19))->toBe('3')
            ->and(DatevBuKeyMapping::toCsvValue(7, isExpense: true))->toBe('8')
            ->and(DatevBuKeyMapping::toCsvValue(19, isExpense: true))->toBe('9');
    });

});

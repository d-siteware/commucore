<?php

declare(strict_types=1);

use App\Services\Accounting\Datev\DatevBuKeyMapping;

describe('DatevBuKeyMapping', function (): void {

    it('returns null for 0% vat (steuerfrei)', function (): void {
        expect(DatevBuKeyMapping::fromVatPercent(0))->toBeNull();
    });

    it('returns "8" for 7% vat (ermäßigter Steuersatz)', function (): void {
        expect(DatevBuKeyMapping::fromVatPercent(7))->toBe('8');
    });

    it('returns "9" for 19% vat (Regelsteuersatz)', function (): void {
        expect(DatevBuKeyMapping::fromVatPercent(19))->toBe('9');
    });

    it('returns null for unknown vat rates', function (): void {
        expect(DatevBuKeyMapping::fromVatPercent(5))->toBeNull();
        expect(DatevBuKeyMapping::fromVatPercent(16))->toBeNull();
    });

    it('returns empty string for toCsvValue with 0% vat', function (): void {
        expect(DatevBuKeyMapping::toCsvValue(0))->toBe('');
    });

    it('returns BU-Schlüssel string for toCsvValue with 7%', function (): void {
        expect(DatevBuKeyMapping::toCsvValue(7))->toBe('8');
    });

    it('returns BU-Schlüssel string for toCsvValue with 19%', function (): void {
        expect(DatevBuKeyMapping::toCsvValue(19))->toBe('9');
    });

});

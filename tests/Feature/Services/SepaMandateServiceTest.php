<?php

declare(strict_types=1);

use App\Enums\SepaMandateStatus;
use App\Enums\SepaMandateType;
use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;
use App\Services\Sepa\SepaMandateService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function mandateService(): SepaMandateService
{
    return app(SepaMandateService::class);
}

describe('create', function (): void {

    it('creates an active CORE mandate', function (): void {
        $member = Member::factory()->create();

        $mandate = mandateService()->create(
            member: $member,
            iban: 'DE89370400440532013000',
            accountHolder: 'Max Mustermann',
            bic: 'COBADEFFXXX',
            type: SepaMandateType::Core,
        );

        expect($mandate)->toBeInstanceOf(SepaMandate::class);
        expect($mandate->member_id)->toBe($member->id);
        expect($mandate->iban)->toBe('DE89370400440532013000');
        expect($mandate->account_holder)->toBe('Max Mustermann');
        expect($mandate->bic)->toBe('COBADEFFXXX');
        expect($mandate->mandate_type)->toBe(SepaMandateType::Core);
        expect($mandate->status)->toBe(SepaMandateStatus::Active);
        expect($mandate->mandate_reference)->not->toBeNull();
    });

    it('creates an active B2B mandate', function (): void {
        $member = Member::factory()->create();

        $mandate = mandateService()->create(
            member: $member,
            iban: 'DE89370400440532013000',
            accountHolder: 'Firma GmbH',
            type: SepaMandateType::B2b,
        );

        expect($mandate->mandate_type)->toBe(SepaMandateType::B2b);
    });

    it('updates member IBAN and BIC from mandate', function (): void {
        $member = Member::factory()->create();

        mandateService()->create(
            member: $member,
            iban: 'DE89370400440532013000',
            accountHolder: 'Max Mustermann',
            bic: 'COBADEFFXXX',
        );

        $member->refresh();
        expect($member->iban)->toBe('DE89370400440532013000');
        expect($member->bic)->toBe('COBADEFFXXX');
        expect($member->account_holder)->toBe('Max Mustermann');
    });

});

describe('cancel', function (): void {

    it('cancels an active mandate', function (): void {
        $member = Member::factory()->create();
        $mandate = SepaMandate::factory()->for($member)->create();

        mandateService()->cancel($mandate);

        $mandate->refresh();
        expect($mandate->status)->toBe(SepaMandateStatus::Cancelled);
    });

});

describe('getActiveMandate', function (): void {

    it('returns active mandate', function (): void {
        $member = Member::factory()->create();
        $mandate = SepaMandate::factory()->for($member)->create();

        $result = mandateService()->getActiveMandate($member);

        expect($result)->not->toBeNull();
        expect($result->id)->toBe($mandate->id);
    });

    it('returns null when no active mandate exists', function (): void {
        $member = Member::factory()->create();

        $result = mandateService()->getActiveMandate($member);

        expect($result)->toBeNull();
    });

    it('ignores cancelled mandates', function (): void {
        $member = Member::factory()->create();
        SepaMandate::factory()->for($member)->cancelled()->create();

        $result = mandateService()->getActiveMandate($member);

        expect($result)->toBeNull();
    });

    it('returns latest active mandate when multiple exist', function (): void {
        $member = Member::factory()->create();
        $oldMandate = SepaMandate::factory()->for($member)->create([
            'created_at' => now()->subYear(),
        ]);
        $newMandate = SepaMandate::factory()->for($member)->create([
            'created_at' => now(),
        ]);

        $result = mandateService()->getActiveMandate($member);

        expect($result->id)->toBe($newMandate->id);
    });

});

describe('hasActiveMandate', function (): void {

    it('returns true when active mandate exists', function (): void {
        $member = Member::factory()->create();
        SepaMandate::factory()->for($member)->create();

        expect(mandateService()->hasActiveMandate($member))->toBeTrue();
    });

    it('returns false when no active mandate', function (): void {
        $member = Member::factory()->create();

        expect(mandateService()->hasActiveMandate($member))->toBeFalse();
    });

});

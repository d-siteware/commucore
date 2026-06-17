<?php

declare(strict_types=1);

use App\Enums\MemberFeeType;
use App\Models\Accounting\Account;
use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;
use App\Services\SettingsService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    Cache::flush();

    $this->account = Account::factory()->create([
        'name' => 'Test-Vereinskonto',
        'iban' => 'DE89370400440532013000',
        'bic' => 'COBADEFFXXX',
        'institute' => 'Testbank',
    ]);

    $settings = app(SettingsService::class);
    $settings->set('sepa.creditor_id', 'DE00ZZZ00000000000');
    $settings->set('sepa.creditor_account_id', $this->account->id, 'integer');
});

// ─── setup ─────────────────────────────────────────────────────────────────

it('fails when sepa is not configured', function (): void {
    app(SettingsService::class)->resetGroup('sepa');

    $this->artisan('commucore:collect-sepa-fees')
        ->expectsOutputToContain('SEPA-Einstellungen sind nicht konfiguriert')
        ->assertFailed();
});

// ─── --create-transactions ─────────────────────────────────────────────────

describe('--create-transactions', function (): void {

    it('creates transactions and shows them in pending table', function (): void {
        $member = Member::factory()->create([
            'fee_type' => MemberFeeType::FULL,
        ]);
        SepaMandate::factory()->for($member)->create();

        $this->artisan('commucore:collect-sepa-fees --create-transactions --dry-run')
            ->expectsOutputToContain('offene Beitrags-Transaktionen')
            ->expectsOutputToContain($member->fullName())
            ->assertSuccessful();

        $this->assertDatabaseHas('member_transactions', [
            'member_id' => $member->id,
            'is_membership_fee' => true,
            'fee_year' => now()->year,
        ]);
    });

    it('does not create duplicate transactions on second run', function (): void {
        $member = Member::factory()->create([
            'fee_type' => MemberFeeType::FULL,
        ]);
        SepaMandate::factory()->for($member)->create();

        $this->artisan('commucore:collect-sepa-fees --create-transactions --store')
            ->assertSuccessful();

        $this->artisan('commucore:collect-sepa-fees --create-transactions --dry-run')
            ->expectsOutputToContain('Keine neuen offenen Beitragszahlungen')
            ->assertSuccessful();

        expect(\App\Models\Membership\MemberTransaction::where('member_id', $member->id)->count())->toBe(1);
    });

});

// ─── --store ───────────────────────────────────────────────────────────────

describe('--store', function (): void {

    it('generates and stores XML file', function (): void {
        $member = Member::factory()->create([
            'fee_type' => MemberFeeType::FULL,
        ]);
        SepaMandate::factory()->for($member)->create();

        $this->artisan('commucore:collect-sepa-fees --create-transactions --store')
            ->expectsOutputToContain('XML gespeichert')
            ->assertSuccessful();
    });

});

// ─── no eligible members ───────────────────────────────────────────────────

it('shows warning when no pending collections exist', function (): void {
    $this->artisan('commucore:collect-sepa-fees')
        ->expectsOutputToContain('Keine offenen Beitragszahlungen')
        ->assertSuccessful();
});

it('shows warning when --create-transactions finds no eligible members', function (): void {
    $this->artisan('commucore:collect-sepa-fees --create-transactions --dry-run')
        ->expectsOutputToContain('Keine neuen offenen Beitragszahlungen')
        ->assertSuccessful();
});

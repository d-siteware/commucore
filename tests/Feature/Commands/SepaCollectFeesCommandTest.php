<?php

declare(strict_types=1);

use App\Enums\MemberFeeType;
use App\Models\Accounting\Account;
use App\Models\Accounting\FiscalYear;
use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;
use App\Models\Sepa\SepaCollectionAttempt;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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

it('fails when sepa is not configured', function (): void {
    app(SettingsService::class)->resetGroup('sepa');

    $this->artisan('commucore:collect-sepa-fees')
        ->expectsOutputToContain('SEPA-Einstellungen sind nicht konfiguriert')
        ->assertFailed();
});

it('shows warning when no open candidates exist', function (): void {
    FiscalYear::factory()->create();
    $this->artisan('commucore:collect-sepa-fees')
        ->expectsOutputToContain('Keine offenen Beitragszahlungen')
        ->assertSuccessful();
});

it('shows candidates and generates XML on dry run without writing', function (): void {
    FiscalYear::factory()->create();

    $member = Member::factory()->create([
        'fee_type' => MemberFeeType::FULL,
    ]);
    SepaMandate::factory()->for($member)->create();

    $this->artisan('commucore:collect-sepa-fees --dry-run')
        ->expectsOutputToContain($member->fullName())
        ->expectsOutputToContain('Dry-Run')
        ->expectsOutputToContain('5,00 €')
        ->assertSuccessful();

    $this->assertDatabaseCount('sepa_collection_attempts', 0);
});

it('creates attempts and generates XML with --store', function (): void {
    FiscalYear::factory()->create(['year' => now()->year, 'closed_at' => null, 'opened_at' => now()->subYear()]);

    $member = Member::factory()->create([
        'fee_type' => MemberFeeType::FULL,
    ]);

    SepaMandate::factory()->for($member)->create();

    $this->artisan('commucore:collect-sepa-fees --store')
        ->expectsOutputToContain('XML gespeichert')
        ->assertSuccessful();

    $this->assertDatabaseHas('sepa_collection_attempts', [
        'member_id' => $member->id,
        'period_key' => now()->year,
    ]);
});

it('does not create duplicate attempts on second run', function (): void {

    FiscalYear::factory()->create();

    $member = Member::factory()->create([
        'fee_type' => MemberFeeType::FULL,
    ]);
    SepaMandate::factory()->for($member)->create();

    $this->artisan('commucore:collect-sepa-fees --store')
        ->assertSuccessful();

    $this->artisan('commucore:collect-sepa-fees --dry-run')
        ->expectsOutputToContain('Keine offenen Beitragszahlungen')
        ->assertSuccessful();

    expect(SepaCollectionAttempt::where('member_id', $member->id)->count())->toBe(1);
});

it('fails with EBICS when ebics is not configured', function (): void {
    /**
     *  TODO: Skipped until EBICS is fully implemented
     */
    $this->artisan('commucore:collect-sepa-fees --ebics-upload')
        ->expectsOutputToContain('EBICS ist nicht konfiguriert')
        ->assertFailed();
})->skip();

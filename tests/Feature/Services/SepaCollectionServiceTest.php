<?php

declare(strict_types=1);

use App\Enums\MemberFeeType;
use App\Enums\TransactionStatus;
use App\Models\Accounting\Account;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use App\Models\Membership\SepaMandate;
use App\Services\Sepa\SepaCollectionService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Cache;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function (): void {
    Cache::flush();
});

// ─── helpers ───────────────────────────────────────────────────────────────

function createCreditAccount(): Account
{
    return Account::factory()->create([
        'name' => 'Test-Vereinskonto',
        'iban' => 'DE89370400440532013000',
        'bic' => 'COBADEFFXXX',
        'institute' => 'Testbank',
    ]);
}

function createSepaSettings(Account $account): void
{
    app(SettingsService::class)->set('sepa.creditor_id', 'DE00ZZZ00000000000');
    app(SettingsService::class)->set('sepa.creditor_account_id', $account->id, 'integer');
}

function service(): SepaCollectionService
{
    return app(SepaCollectionService::class);
}

// ─── createFeeTransactions ─────────────────────────────────────────────────

describe('createFeeTransactions', function (): void {

    it('creates transactions for members without existing fee transaction', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create();

        $result = service()->createFeeTransactions(year: now()->year);

        expect($result)->toHaveCount(1);

        $memberTx = $result->first();
        expect($memberTx->transaction->status)->toBe(TransactionStatus::submitted)
            ->and($memberTx->fee_year)->toBe(now()->year)
            ->and($memberTx->is_membership_fee)->toBeTrue()
            ->and($memberTx->transaction->amount_net)->toBe(6000)
            ->and($memberTx->member->id)->toBe($member->id);
    });

    it('skips members who already have a fee transaction for the year', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create();
        MemberTransaction::factory()->create([
            'member_id' => $member->id,
            'is_membership_fee' => true,
            'fee_year' => now()->year,
        ]);

        $result = service()->createFeeTransactions(year: now()->year);

        expect($result)->toHaveCount(0);
    });

    it('skips free-fee members', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FREE]);
        SepaMandate::factory()->for($member)->create();

        $result = service()->createFeeTransactions(year: now()->year);

        expect($result)->toHaveCount(0);
    });

    it('skips members without active mandate', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->cancelled()->create();

        $result = service()->createFeeTransactions(year: now()->year);

        expect($result)->toHaveCount(0);
    });

    it('calculates discounted fee correctly', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::DISC]);
        SepaMandate::factory()->for($member)->create();

        $result = service()->createFeeTransactions(year: now()->year);

        expect($result)->toHaveCount(1);
        expect($result->first()->transaction->amount_net)->toBe(3600);
    });

    it('throws when no creditor account is configured', function (): void {
        service()->createFeeTransactions(year: now()->year);
    })->throws(\RuntimeException::class, 'SEPA creditor account is not configured.');

});

// ─── generateXml ──────────────────────────────────────────────────────────

describe('generateXml', function (): void {

    it('generates valid SEPA XML for given member transactions', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create();

        $memberTransactions = service()->createFeeTransactions(year: now()->year);
        $xml = service()->generateXml($memberTransactions);

        expect($xml)->toBeString()
            ->and($xml)->toContain('<?xml')
            ->and($xml)->toContain('pain.008')
            ->and($xml)->toContain($member->fullName())
            ->and($xml)->toContain('60.00');
    });

    it('throws when a member has no active mandate', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create();

        $memberTransactions = service()->createFeeTransactions(year: now()->year);

        $member->sepaMandates()->delete();

        service()->generateXml($memberTransactions);
    })->throws(\RuntimeException::class);

});

// ─── markAsBooked ─────────────────────────────────────────────────────────

describe('markAsBooked', function (): void {

    it('marks transactions as booked', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create();

        $memberTransactions = service()->createFeeTransactions(year: now()->year);

        expect($memberTransactions->first()->transaction->status)->toBe(TransactionStatus::submitted);

        service()->markAsBooked($memberTransactions);

        $fresh = $memberTransactions->first()->fresh();
        expect($fresh->transaction->status)->toBe(TransactionStatus::booked);

        $mandate = $member->activeSepaMandate()->first();
        expect($mandate->payment_completed_at)->toBeNull();
    });

});

// ─── collect ──────────────────────────────────────────────────────────────

describe('collect', function (): void {

    it('creates transactions and generates XML in one step', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create();

        $result = service()->collect(year: now()->year);

        expect($result['transactions'])->toHaveCount(1);
        expect($result['xml'])->toBeString()
            ->and($result['xml'])->toContain('<?xml')
            ->and($result['xml'])->toContain('pain.008');
    });

    it('returns null xml when no members qualify', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $result = service()->collect(year: now()->year);

        expect($result['transactions'])->toHaveCount(0);
        expect($result['xml'])->toBeNull();
    });

});

// ─── uploadToEbics ────────────────────────────────────────────────────────

describe('uploadToEbics', function (): void {

    it('throws when ebics is not ready', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        service()->uploadToEbics('<xml/>');
    })->throws(\RuntimeException::class, 'EBICS is not ready for upload');

});

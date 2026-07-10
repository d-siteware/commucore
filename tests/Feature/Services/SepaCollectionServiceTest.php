<?php

declare(strict_types=1);

use App\Enums\MemberFeeType;
use App\Enums\SepaCollectionAttemptStatus;
use App\Models\Accounting\Account;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use App\Models\Membership\SepaMandate;
use App\Models\Sepa\SepaCollectionAttempt;
use App\Services\Sepa\SepaCollectionService;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

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
    FiscalYear::factory()->create();
}

function service(): SepaCollectionService
{
    return app(SepaCollectionService::class);
}

// ─── findOpenCandidates ────────────────────────────────────────────────────

describe('findOpenCandidates', function (): void {

    it('returns members without existing fee transaction or pending attempt', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create();

        $candidates = service()->findOpenCandidates(referenceDate: now());

        expect($candidates)->toHaveCount(1);
        expect($candidates->first()->id)->toBe($member->id);
    });

    it('excludes members who already have a membership fee transaction for the year', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create();
        $transaction = Transaction::factory()->create([
            'account_id' => $account->id,
            'type' => \App\Enums\TransactionType::Deposit,
            'status' => \App\Enums\TransactionStatus::booked,
            'amount_net' => 1_000_00,
            'amount_gross' => 1_000_00,
        ]);
        MemberTransaction::factory()->create([
            'member_id' => $member->id,
            'transaction_id' => $transaction->id,
            'is_membership_fee' => true,
            'fee_year' => now()->year,
        ]);

        $candidates = service()->findOpenCandidates(referenceDate: now());

        expect($candidates)->toHaveCount(0);
    });

    it('excludes members with a pending attempt for the year', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create();
        SepaCollectionAttempt::factory()->for($member)->for($mandate, 'sepaMandate')->create(['period_key' => now()->year]);

        $candidates = service()->findOpenCandidates(referenceDate: now());

        expect($candidates)->toHaveCount(0);
    });

    it('excludes free-fee members', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FREE]);
        SepaMandate::factory()->for($member)->create();

        $candidates = service()->findOpenCandidates(referenceDate: now());

        expect($candidates)->toHaveCount(0);
    });

    it('excludes members without active mandate', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->cancelled()->create();

        $candidates = service()->findOpenCandidates(referenceDate: now());

        expect($candidates)->toHaveCount(0);
    });

    it('excludes inactive members', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create([
            'fee_type' => MemberFeeType::FULL,
            'left_at' => now(),
        ]);
        SepaMandate::factory()->for($member)->create();

        $candidates = service()->findOpenCandidates(referenceDate: now());

        expect($candidates)->toHaveCount(0);
    });

    it('filters candidates by the given year', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create();
        $transaction = Transaction::factory()->create(['account_id' => $account->id]);
        MemberTransaction::factory()->create([
            'member_id' => $member->id,
            'transaction_id' => $transaction->id,
            'is_membership_fee' => true,
            'fee_year' => now()->year - 1,
        ]);

        $candidatesForExcludedYear = service()->findOpenCandidates(referenceDate: now()->subYear());
        expect($candidatesForExcludedYear)->toHaveCount(0);

        $candidatesForCurrentYear = service()->findOpenCandidates(referenceDate: now());
        expect($candidatesForCurrentYear)->toHaveCount(1);
    });

    it('throws when no creditor account is configured', function (): void {
        FiscalYear::factory()->create();
        service()->findOpenCandidates(referenceDate: now());
    })->throws(RuntimeException::class, 'SEPA creditor account is not configured.');

});

// ─── createAttemptsAndGenerateXml ──────────────────────────────────────────

describe('createAttemptsAndGenerateXml', function (): void {

    it('creates attempts and returns valid XML', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create();

        $result = service()->createAttemptsAndGenerateXml(
            members: collect([$member]),
            referenceDate: now(),
        );

        expect($result['xml'])->toBeString()->toContain('<?xml')->toContain('pain.008');
        expect($result['attempts'])->toHaveCount(1);
        expect($result['attempts']->first())->toBeInstanceOf(SepaCollectionAttempt::class);
        expect($result['attempts']->first()->status)->toBe(SepaCollectionAttemptStatus::Submitted);
        expect($result['attempts']->first()->amount)->toBe(500);

        expect($result['validation'])->not->toBeNull();
        expect($result['validation']->valid)->toBeTrue();
    });

    it('returns null xml when no members given', function (): void {
        $result = service()->createAttemptsAndGenerateXml(
            members: collect(),
            referenceDate: now(),
        );

        expect($result['xml'])->toBeNull();
        expect($result['attempts'])->toHaveCount(0);
        expect($result['validation'])->toBeNull();
    });

    it('creates attempts with FRST sequence for first-time collections', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create(['last_used_at' => null]);

        $result = service()->createAttemptsAndGenerateXml(
            members: collect([$member]),
            referenceDate: now(),
        );

        expect($result['xml'])->toContain('FRST');
    });

    it('creates attempts with RCUR sequence for recurring collections', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create(['last_used_at' => now()->subYear()]);

        $result = service()->createAttemptsAndGenerateXml(
            members: collect([$member]),
            referenceDate: now(),
        );

        expect($result['xml'])->toContain('RCUR');
    });

    it('throws when a member has no active mandate', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);

        service()->createAttemptsAndGenerateXml(
            members: collect([$member]),
            referenceDate: now(),
        );
    })->throws(RuntimeException::class, 'has no active SEPA mandate');

    it('assigns batch reference to created attempts', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        SepaMandate::factory()->for($member)->create();

        $result = service()->createAttemptsAndGenerateXml(
            members: collect([$member]),
            referenceDate: now(),
        );

        $attempt = $result['attempts']->first();
        expect($attempt->batch_reference)->not->toBeNull();
    });

});

// ─── confirm ───────────────────────────────────────────────────────────────

describe('confirm', function (): void {

    it('creates a Transaction and MemberTransaction for a submitted attempt', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create();
        $attempt = SepaCollectionAttempt::factory()->for($member)->for($mandate, 'sepaMandate')->create();

        $transaction = service()->confirm($attempt);

        expect($transaction->id)->not->toBeNull();
        expect($transaction->amount_net)->toBe(6000);
        expect($transaction->label)->toContain('SEPA-Lastschrift');

        $this->assertDatabaseHas('member_transactions', [
            'member_id' => $member->id,
            'transaction_id' => $transaction->id,
            'sepa_mandate_id' => $mandate->id,
            'is_membership_fee' => true,
            'fee_year' => now()->year,
        ]);

        $attempt->refresh();
        expect($attempt->status)->toBe(SepaCollectionAttemptStatus::Confirmed);
        expect($attempt->resolved_at)->not->toBeNull();
        expect($attempt->transaction_id)->toBe($transaction->id);
    });

    it('throws for already confirmed attempts', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create();
        $attempt = SepaCollectionAttempt::factory()->for($member)->for($mandate, 'sepaMandate')->confirmed()->create();

        service()->confirm($attempt);
    })->throws(RuntimeException::class, 'Only submitted attempts can be confirmed.');

    it('throws when no creditor account is configured', function (): void {
        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create();
        $attempt = SepaCollectionAttempt::factory()->for($member)->for($mandate, 'sepaMandate')->create();

        service()->confirm($attempt);
    })->throws(RuntimeException::class, 'SEPA creditor account is not configured.');
});

// ─── confirmBatch ──────────────────────────────────────────────────────────

describe('confirmBatch', function (): void {

    it('confirms all unresolved attempts in a batch', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $member1 = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $member2 = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate1 = SepaMandate::factory()->for($member1)->create();
        $mandate2 = SepaMandate::factory()->for($member2)->create();

        $batchRef = 'BATCH-TEST-001';

        SepaCollectionAttempt::factory()
            ->for($member1)->for($mandate1, 'sepaMandate')
            ->withBatch($batchRef)->create();
        SepaCollectionAttempt::factory()
            ->for($member2)->for($mandate2, 'sepaMandate')
            ->withBatch($batchRef)->create();

        $transactions = service()->confirmBatch($batchRef);

        expect($transactions)->toHaveCount(2);

        expect(SepaCollectionAttempt::query()->unresolved()->count())->toBe(0);
    });

    it('does nothing for empty batch', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        $transactions = service()->confirmBatch('NONEXISTENT');

        expect($transactions)->toHaveCount(0);
    });

});

// ─── uploadToEbics ─────────────────────────────────────────────────────────

describe('uploadToEbics', function (): void {

    it('throws when ebics is not ready', function (): void {
        $account = createCreditAccount();
        createSepaSettings($account);

        service()->uploadToEbics('<xml/>');
    })->throws(RuntimeException::class, 'EBICS is not ready for upload');

});

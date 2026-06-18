<?php

declare(strict_types=1);

use App\Enums\MemberFeeType;
use App\Enums\SepaCollectionAttemptStatus;
use App\Enums\SepaMandateType;
use App\Models\Accounting\Account;
use App\Models\Membership\Member;
use App\Models\Sepa\SepaCollectionAttempt;
use App\Notifications\SepaReturnDebitNotification;
use App\Services\Sepa\SepaReturnDebitService;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createSepaAccount(): Account
{
    return Account::factory()->create([
        'name' => 'Test-Vereinskonto',
        'iban' => 'DE89370400440532013000',
        'bic' => 'COBADEFFXXX',
        'institute' => 'Testbank',
    ]);
}

function configureSepa(Account $account): void
{
    app(SettingsService::class)->set('sepa.creditor_id', 'DE00ZZZ00000000000');
    app(SettingsService::class)->set('sepa.creditor_account_id', $account->id, 'integer');
}

function returnService(): SepaReturnDebitService
{
    return app(SepaReturnDebitService::class);
}

function createReturnedAttempt(Member $member): SepaCollectionAttempt
{
    $account = createSepaAccount();
    configureSepa($account);

    $mandate = \App\Models\Membership\SepaMandate::factory()->for($member)->create();
    $cbAccount = \App\Models\Accounting\Account::factory()->create();
    app(\App\Services\SettingsService::class)->set('sepa.creditor_account_id', $cbAccount->id, 'integer');

    $attempt = SepaCollectionAttempt::factory()
        ->for($member)
        ->for($mandate, 'sepaMandate')
        ->returned()
        ->create();

    return $attempt;
}

describe('handleReturn', function (): void {

    it('marks a submitted attempt as returned and sends notification', function (): void {
        $account = createSepaAccount();
        configureSepa($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = \App\Models\Membership\SepaMandate::factory()->for($member)->create();
        $attempt = SepaCollectionAttempt::factory()
            ->for($member)
            ->for($mandate, 'sepaMandate')
            ->create();

        Notification::fake();

        returnService()->handleReturn(
            attempt: $attempt,
            returnReason: 'Nicht gedeckt',
        );

        $attempt->refresh();
        expect($attempt->status)->toBe(SepaCollectionAttemptStatus::Returned);
        expect($attempt->return_reason)->toContain('Rücklastschrift');
        expect($attempt->return_reason)->toContain('Nicht gedeckt');

        expect($mandate->fresh()->last_used_at)->toBeNull();

        Notification::assertSentTo($member, SepaReturnDebitNotification::class);
    });

    it('stores return reference when provided', function (): void {
        $account = createSepaAccount();
        configureSepa($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = \App\Models\Membership\SepaMandate::factory()->for($member)->create();
        $attempt = SepaCollectionAttempt::factory()
            ->for($member)
            ->for($mandate, 'sepaMandate')
            ->create();

        returnService()->handleReturn(
            attempt: $attempt,
            returnReason: 'Nicht gedeckt',
            returnReference: 'REF-12345',
        );

        $attempt->refresh();
        expect($attempt->return_reason)->toContain('REF-12345');
    });

});

describe('recollect', function (): void {

    it('creates a new submitted attempt for a returned fee', function (): void {
        $account = createSepaAccount();
        configureSepa($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = \App\Models\Membership\SepaMandate::factory()->for($member)->create();
        $attempt = SepaCollectionAttempt::factory()
            ->for($member)
            ->for($mandate, 'sepaMandate')
            ->returned()
            ->create();

        $result = returnService()->recollect($attempt);

        expect($result['xml'])->toBeString()->toContain('<?xml');
        expect($result['attempts'])->toHaveCount(1);

        $newAttempt = $result['attempts']->first();
        expect($newAttempt)->toBeInstanceOf(SepaCollectionAttempt::class);
        expect($newAttempt->status)->toBe(SepaCollectionAttemptStatus::Submitted);
        expect($newAttempt->amount)->toBe(6000);
        expect($newAttempt->remittance_information)->toContain('Wiedereinzug');
    });

    it('rejects B2B re-collection with exception', function (): void {
        $account = createSepaAccount();
        configureSepa($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = \App\Models\Membership\SepaMandate::factory()->for($member)->b2b()->create();
        $attempt = SepaCollectionAttempt::factory()
            ->for($member)
            ->for($mandate, 'sepaMandate')
            ->returned()
            ->create();

        returnService()->recollect($attempt);
    })->throws(\RuntimeException::class, 'Re-collection is not available for B2B mandates');

    it('rejects re-collection older than 30 days', function (): void {
        $account = createSepaAccount();
        configureSepa($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = \App\Models\Membership\SepaMandate::factory()->for($member)->create();
        $attempt = SepaCollectionAttempt::factory()
            ->for($member)
            ->for($mandate, 'sepaMandate')
            ->returned()
            ->create([
                'resolved_at' => now()->subDays(31),
            ]);

        returnService()->recollect($attempt);
    })->throws(\RuntimeException::class, 'Re-collection window has expired');

    it('rejects recollect when a pending attempt already exists', function (): void {
        $account = createSepaAccount();
        configureSepa($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = \App\Models\Membership\SepaMandate::factory()->for($member)->create();
        $returnedAttempt = SepaCollectionAttempt::factory()
            ->for($member)
            ->for($mandate, 'sepaMandate')
            ->returned()
            ->create();

        SepaCollectionAttempt::factory()
            ->for($member)
            ->for($mandate, 'sepaMandate')
            ->create(['fee_year' => $returnedAttempt->fee_year]);

        returnService()->recollect($returnedAttempt);
    })->throws(\RuntimeException::class, 'There is already a pending collection attempt');

});

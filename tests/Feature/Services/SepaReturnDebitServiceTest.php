<?php

declare(strict_types=1);

use App\Enums\AccountType;
use App\Enums\MemberFeeType;
use App\Enums\SepaMandateType;
use App\Enums\TransactionStatus;
use App\Models\Accounting\Account;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use App\Models\Membership\SepaMandate;
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
        'type' => AccountType::bank,
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

function buildFeeTransaction(Member $member, Account $account, SepaMandate $mandate): MemberTransaction
{
    $tx = Transaction::factory()->create([
        'account_id' => $account->id,
        'status' => TransactionStatus::booked,
        'amount_gross' => 6000,
        'amount_net' => 6000,
        'date' => now(),
    ]);

    return MemberTransaction::factory()->create([
        'member_id' => $member->id,
        'transaction_id' => $tx->id,
        'sepa_mandate_id' => $mandate->id,
        'is_membership_fee' => true,
        'fee_year' => now()->year,
    ]);
}

describe('handleReturn', function (): void {

    it('marks a booked transaction as returned and sends notification', function (): void {
        $account = createSepaAccount();
        configureSepa($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create();
        $memberTx = buildFeeTransaction($member, $account, $mandate);

        Notification::fake();

        returnService()->handleReturn(
            transaction: $memberTx->transaction,
            member: $member,
            returnReason: 'Nicht gedeckt',
        );

        $memberTx->transaction->refresh();
        expect($memberTx->transaction->status)->toBe(TransactionStatus::returned);
        expect($memberTx->transaction->description)->toContain('Rücklastschrift');

        Notification::assertSentTo($member, SepaReturnDebitNotification::class);
    });

});

describe('recollect', function (): void {

    it('creates a new submitted transaction for a returned fee', function (): void {
        $account = createSepaAccount();
        configureSepa($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create();
        $memberTx = buildFeeTransaction($member, $account, $mandate);

        returnService()->handleReturn(
            transaction: $memberTx->transaction,
            member: $member,
            returnReason: 'Nicht gedeckt',
        );

        $newTx = returnService()->recollect(
            returnedTransaction: $memberTx->transaction,
            member: $member,
        );

        expect($newTx)->toBeInstanceOf(Transaction::class);
        expect($newTx->status)->toBe(TransactionStatus::submitted);
        expect($newTx->label)->toContain('Wiedereinzug');
        expect($newTx->amount_net)->toBe(6000);

        $newMemberTx = MemberTransaction::query()
            ->where('transaction_id', $newTx->id)
            ->first();
        expect($newMemberTx)->not->toBeNull();
        expect($newMemberTx->member_id)->toBe($member->id);
        expect($newMemberTx->sepa_mandate_id)->toBe($mandate->id);
    });

    it('rejects B2B re-collection with exception', function (): void {
        $account = createSepaAccount();
        configureSepa($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create([
            'mandate_type' => SepaMandateType::B2b,
        ]);
        $memberTx = buildFeeTransaction($member, $account, $mandate);

        returnService()->handleReturn(
            transaction: $memberTx->transaction,
            member: $member,
            returnReason: 'B2B Test',
        );

        returnService()->recollect(
            returnedTransaction: $memberTx->transaction,
            member: $member,
        );
    })->throws(RuntimeException::class, 'Re-collection is not available for B2B mandates.');

    it('rejects re-collection older than 30 days', function (): void {
        $account = createSepaAccount();
        configureSepa($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create();
        $memberTx = buildFeeTransaction($member, $account, $mandate);

        $memberTx->transaction->update(['created_at' => now()->subDays(31)]);

        returnService()->handleReturn(
            transaction: $memberTx->transaction,
            member: $member,
            returnReason: 'Alt Test',
        );

        returnService()->recollect(
            returnedTransaction: $memberTx->transaction,
            member: $member,
        );
    })->throws(RuntimeException::class, 'Re-collection window has expired');

    it('uses stored sepa_mandate_id for re-collection', function (): void {
        $account = createSepaAccount();
        configureSepa($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create();
        $memberTx = buildFeeTransaction($member, $account, $mandate);

        returnService()->handleReturn(
            transaction: $memberTx->transaction,
            member: $member,
            returnReason: 'Nicht gedeckt',
        );

        // Cancel old mandate so getActiveMandate would return null
        $mandate->cancel();

        $newTx = returnService()->recollect(
            returnedTransaction: $memberTx->transaction,
            member: $member,
        );

        // Should still succeed because it uses stored sepa_mandate_id
        expect($newTx)->toBeInstanceOf(Transaction::class);
        expect($newTx->status)->toBe(TransactionStatus::submitted);
    });

});

describe('getRecentReturns', function (): void {

    it('returns empty array when no returned transactions exist', function (): void {
        $result = returnService()->getRecentReturns();
        expect($result)->toBeArray();
        expect($result)->toHaveCount(0);
    });

    it('lists recent returned transactions', function (): void {
        $account = createSepaAccount();
        configureSepa($account);

        $member = Member::factory()->create(['fee_type' => MemberFeeType::FULL]);
        $mandate = SepaMandate::factory()->for($member)->create();
        $memberTx = buildFeeTransaction($member, $account, $mandate);

        returnService()->handleReturn(
            transaction: $memberTx->transaction,
            member: $member,
            returnReason: 'Nicht gedeckt',
        );

        $result = returnService()->getRecentReturns();
        expect($result)->toHaveCount(1);
        expect($result[0]['transaction']->id)->toBe($memberTx->transaction->id);
        expect($result[0]['member']->id)->toBe($member->id);
        expect($result[0]['can_recollect'])->toBeTrue();
    });

});

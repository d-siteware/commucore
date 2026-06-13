<?php

declare(strict_types=1);

use App\Models\Accounting\Transaction;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingTransaction;
use App\Models\User;
use App\Policies\FundingTransactionPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createFundingTransaction(): FundingTransaction
{
    $funding = Funding::factory()->create();
    $transaction = Transaction::factory()->create();

    return FundingTransaction::factory()->create([
        'funding_id' => $funding->id,
        'transaction_id' => $transaction->id,
    ]);
}

describe('FundingTransactionPolicy', function (): void {

    it('denies viewAny for any user', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new FundingTransactionPolicy;

        expect($policy->viewAny($user))->toBeFalse();
    });

    it('denies view for any user', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $fundingTransaction = createFundingTransaction();
        $policy = new FundingTransactionPolicy;

        expect($policy->view($user, $fundingTransaction))->toBeFalse();
    });

    it('denies create for any user', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $policy = new FundingTransactionPolicy;

        expect($policy->create($user))->toBeFalse();
    });

    it('denies update for any user', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $fundingTransaction = createFundingTransaction();
        $policy = new FundingTransactionPolicy;

        expect($policy->update($user, $fundingTransaction))->toBeFalse();
    });

    it('denies delete for any user', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $fundingTransaction = createFundingTransaction();
        $policy = new FundingTransactionPolicy;

        expect($policy->delete($user, $fundingTransaction))->toBeFalse();
    });

    it('denies restore for any user', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $fundingTransaction = createFundingTransaction();
        $policy = new FundingTransactionPolicy;

        expect($policy->restore($user, $fundingTransaction))->toBeFalse();
    });

    it('denies forceDelete for any user', function (): void {
        $user = User::factory()->create(['is_admin' => true]);
        $fundingTransaction = createFundingTransaction();
        $policy = new FundingTransactionPolicy;

        expect($policy->forceDelete($user, $fundingTransaction))->toBeFalse();
    });
});

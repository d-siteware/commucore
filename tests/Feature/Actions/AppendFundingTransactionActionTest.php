<?php

declare(strict_types=1);

use App\Actions\Accounting\AppendFundingTransaction;
use App\Models\Accounting\Transaction;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('appends a funding transaction link', function (): void {
    $transaction = Transaction::factory()->create();
    $funding = Funding::factory()->create();

    $result = AppendFundingTransaction::handle($transaction, $funding);

    expect(FundingTransaction::where('transaction_id', $transaction->id)
        ->where('funding_id', $funding->id)
        ->exists()
    )->toBeTrue()
        ->and($result->id)->toBe($transaction->id);
});

it('stores allocated amount when provided', function (): void {
    $transaction = Transaction::factory()->create();
    $funding = Funding::factory()->create();

    AppendFundingTransaction::handle($transaction, $funding, allocatedAmount: 5000);

    $pivot = FundingTransaction::where('transaction_id', $transaction->id)
        ->where('funding_id', $funding->id)
        ->first();

    expect($pivot)->not->toBeNull()
        ->and($pivot->allocated_amount)->toBe(5000);
});

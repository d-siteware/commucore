<?php

declare(strict_types=1);

use App\Models\Accounting\Transaction;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingTransaction;

describe('FundingTransaction model', function (): void {
    it('can be created', function (): void {
        $funding = Funding::factory()->create();
        $transaction = Transaction::factory()->create();

        $ft = FundingTransaction::create([
            'funding_id' => $funding->id,
            'transaction_id' => $transaction->id,
            'allocated_amount' => 5000,
        ]);

        expect($ft)->toBeInstanceOf(FundingTransaction::class)
            ->and($ft->allocated_amount)->toBe(5000);
    });

    it('belongs to a funding', function (): void {
        $funding = Funding::factory()->create();
        $transaction = Transaction::factory()->create();

        $ft = FundingTransaction::create([
            'funding_id' => $funding->id,
            'transaction_id' => $transaction->id,
        ]);

        expect($ft->funding)->toBeInstanceOf(Funding::class)
            ->and($ft->funding->id)->toBe($funding->id);
    });

    it('belongs to a transaction', function (): void {
        $funding = Funding::factory()->create();
        $transaction = Transaction::factory()->create();

        $ft = FundingTransaction::create([
            'funding_id' => $funding->id,
            'transaction_id' => $transaction->id,
        ]);

        expect($ft->transaction)->toBeInstanceOf(Transaction::class)
            ->and($ft->transaction->id)->toBe($transaction->id);
    });

    it('casts allocated_amount as integer', function (): void {
        $funding = Funding::factory()->create();
        $transaction = Transaction::factory()->create();

        $ft = FundingTransaction::create([
            'funding_id' => $funding->id,
            'transaction_id' => $transaction->id,
            'allocated_amount' => 5000,
        ]);

        expect($ft->allocated_amount)->toBeInt();
    });

    it('effectiveAmount returns allocated_amount when set', function (): void {
        $funding = Funding::factory()->create();
        $transaction = Transaction::factory()->create(['amount_gross' => 10000]);

        $ft = FundingTransaction::create([
            'funding_id' => $funding->id,
            'transaction_id' => $transaction->id,
            'allocated_amount' => 5000,
        ]);

        expect($ft->effectiveAmount())->toBe(5000);
    });

    it('effectiveAmount returns transaction amount when not allocated', function (): void {
        $funding = Funding::factory()->create();
        $transaction = Transaction::factory()->create(['amount_gross' => 10000]);

        $ft = FundingTransaction::create([
            'funding_id' => $funding->id,
            'transaction_id' => $transaction->id,
            'allocated_amount' => null,
        ]);

        expect($ft->effectiveAmount())->toBe(10000);
    });
});

<?php

declare(strict_types=1);

use App\Models\Accounting\Receipt;
use App\Models\Accounting\Transaction;

test('a receipt can be created', function (): void {
    $transaction = Transaction::factory()->create();

    $receipt = Receipt::create([
        'transaction_id' => $transaction->id,
        'file_name' => 'receipt_001.pdf',
        'file_name_original' => 'original_receipt.pdf',
    ]);

    expect($receipt)->toBeInstanceOf(Receipt::class)
        ->and($receipt->exists)->toBeTrue()
        ->and($receipt->file_name)->toBe('receipt_001.pdf');
});

test('a receipt belongs to a transaction', function (): void {
    $transaction = Transaction::factory()->create();
    $receipt = Receipt::create([
        'transaction_id' => $transaction->id,
    ]);

    expect($receipt->transaction)->toBeInstanceOf(Transaction::class)
        ->and($receipt->transaction->id)->toBe($transaction->id);
});

test('getPreviewUrl returns a route string', function (): void {
    $transaction = Transaction::factory()->create();
    $receipt = Receipt::create([
        'transaction_id' => $transaction->id,
        'file_name' => 'test.pdf',
    ]);

    $url = $receipt->getPreviewUrl();

    expect($url)->toBeString()
        ->and($url)->toContain('secure-image')
        ->and($url)->toContain('accounting/receipts');
});

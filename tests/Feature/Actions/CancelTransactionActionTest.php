<?php

declare(strict_types=1);

use App\Actions\Accounting\CancelTransaction;
use App\Enums\TransactionType;
use App\Models\Accounting\CancelTransaction as CancelTransactionModel;
use App\Models\Accounting\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('cancels a transaction and creates reversal', function (): void {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create([
        'label' => 'Test Buchung',
        'amount_gross' => 11900,
        'vat' => 1900,
        'amount_net' => 10000,
        'type' => TransactionType::Deposit,
    ]);

    $reversal = CancelTransaction::handle($transaction, [
        'user_id' => $user->id,
        'reason' => 'Fehlerhafte Buchung',
    ]);

    // Original transaction should be marked as reversal
    $transaction->refresh();
    expect($transaction->type)->toBe(TransactionType::Reversal);

    // A cancel transaction record should exist
    expect(CancelTransactionModel::where('transaction_id', $transaction->id)->exists())->toBeTrue();

    // A new reversal transaction should be created with inverted amounts
    expect($reversal)->toBeInstanceOf(Transaction::class)
        ->and($reversal->label)->toBe('STORNO-Test Buchung')
        ->and($reversal->amount_gross)->toBe(-11900)
        ->and($reversal->vat)->toBe(-1900)
        ->and($reversal->amount_net)->toBe(-10000)
        ->and($reversal->type)->toBe(TransactionType::Deposit);
});

it('preserves account and booking account on reversal', function (): void {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create([
        'type' => TransactionType::Deposit,
    ]);
    $originalAccountId = $transaction->account_id;
    $originalBookingAccountId = $transaction->booking_account_id;

    $reversal = CancelTransaction::handle($transaction, [
        'user_id' => $user->id,
        'reason' => 'Test',
    ]);

    expect($reversal->account_id)->toBe($originalAccountId)
        ->and($reversal->booking_account_id)->toBe($originalBookingAccountId);
});

it('records who cancelled the transaction', function (): void {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create([
        'type' => TransactionType::Deposit,
    ]);

    CancelTransaction::handle($transaction, [
        'user_id' => $user->id,
        'reason' => 'Test',
    ]);

    $cancelRecord = CancelTransactionModel::where('transaction_id', $transaction->id)->first();

    expect($cancelRecord)->not->toBeNull()
        ->and($cancelRecord->user_id)->toBe($user->id)
        ->and($cancelRecord->reason)->toBe('Test');
});

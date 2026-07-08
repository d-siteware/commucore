<?php

declare(strict_types=1);

use App\Actions\Accounting\CancelTransaction;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\CancelTransaction as CancelTransactionModel;
use App\Models\Accounting\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('cancels a transaction and creates reversal booking', function (): void {
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

    // Original transaction must remain untouched (type, amounts, status)
    $transaction->refresh();
    expect($transaction->type)->toBe(TransactionType::Deposit)
        ->and($transaction->amount_gross)->toBe(11900)
        ->and($transaction->status)->toBe(TransactionStatus::booked);

    // A cancel transaction record should exist and link both bookings
    $audit = CancelTransactionModel::where('transaction_id', $transaction->id)->first();
    expect($audit)->not->toBeNull()
        ->and($audit->reversal_transaction_id)->toBe($reversal->id);

    // The reversal booking keeps the original type with inverted amounts,
    // so balances AND type-based reports (income = sum of deposits) neutralize
    expect($reversal)->toBeInstanceOf(Transaction::class)
        ->and($reversal->label)->toBe('STORNO-Test Buchung')
        ->and($reversal->amount_gross)->toBe(-11900)
        ->and($reversal->vat)->toBe(-1900)
        ->and($reversal->amount_net)->toBe(-10000)
        ->and($reversal->type)->toBe(TransactionType::Deposit)
        ->and($reversal->status)->toBe(TransactionStatus::booked);
});

it('neutralizes the account balance', function (): void {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create([
        'amount_gross' => 10000,
        'vat' => 0,
        'amount_net' => 10000,
        'type' => TransactionType::Deposit,
        'status' => TransactionStatus::booked,
    ]);

    $account = $transaction->account;
    $balanceBefore = $account->accountBalance();

    CancelTransaction::handle($transaction, [
        'user_id' => $user->id,
        'reason' => 'Storno-Test',
    ]);

    expect($account->accountBalance())->toBe($balanceBefore - 10000);
});

it('neutralizes the account balance for withdrawals', function (): void {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create([
        'amount_gross' => 5000,
        'vat' => 0,
        'amount_net' => 5000,
        'type' => TransactionType::Withdrawal,
        'status' => TransactionStatus::booked,
    ]);

    $account = $transaction->account;
    $balanceBefore = $account->accountBalance();

    CancelTransaction::handle($transaction, [
        'user_id' => $user->id,
        'reason' => 'Storno-Test',
    ]);

    expect($account->accountBalance())->toBe($balanceBefore + 5000);
});

it('prevents cancelling a transaction twice', function (): void {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create([
        'type' => TransactionType::Deposit,
    ]);

    CancelTransaction::handle($transaction, [
        'user_id' => $user->id,
        'reason' => 'Erster Storno',
    ]);

    CancelTransaction::handle($transaction->refresh(), [
        'user_id' => $user->id,
        'reason' => 'Zweiter Storno',
    ]);
})->throws(RuntimeException::class);

it('prevents cancelling a reversal booking', function (): void {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->create([
        'type' => TransactionType::Deposit,
    ]);

    $reversal = CancelTransaction::handle($transaction, [
        'user_id' => $user->id,
        'reason' => 'Storno',
    ]);

    CancelTransaction::handle($reversal, [
        'user_id' => $user->id,
        'reason' => 'Storno des Stornos',
    ]);
})->throws(RuntimeException::class);

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

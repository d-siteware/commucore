<?php

declare(strict_types=1);

use App\Enums\BookingAccountArea;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Accounting\Account;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\Transaction;
use Carbon\Carbon;

test('a transaction can be created via factory', function (): void {
    $transaction = Transaction::factory()->create();

    expect($transaction)->toBeInstanceOf(Transaction::class)
        ->and($transaction->exists)->toBeTrue();
});

test('transaction type is cast to enum', function (): void {
    $transaction = Transaction::factory()->create(['type' => TransactionType::Deposit]);

    expect($transaction->type)->toBeInstanceOf(TransactionType::class)
        ->and($transaction->type)->toBe(TransactionType::Deposit);
});

test('transaction status is cast to enum', function (): void {
    $transaction = Transaction::factory()->create(['status' => TransactionStatus::booked]);

    expect($transaction->status)->toBeInstanceOf(TransactionStatus::class)
        ->and($transaction->status)->toBe(TransactionStatus::booked);
});

test('transaction area is cast to enum when set', function (): void {
    $transaction = Transaction::factory()->create(['area' => BookingAccountArea::IDEAL]);

    expect($transaction->area)->toBeInstanceOf(BookingAccountArea::class)
        ->and($transaction->area)->toBe(BookingAccountArea::IDEAL);
});

test('transaction date is cast to Carbon', function (): void {
    $transaction = Transaction::factory()->create();

    expect($transaction->date)->toBeInstanceOf(Carbon::class);
});

test('transaction belongs to an account', function (): void {
    $account = Account::factory()->create();
    $transaction = Transaction::factory()->create(['account_id' => $account->id]);

    expect($transaction->account)->toBeInstanceOf(Account::class)
        ->and($transaction->account->id)->toBe($account->id);
});

test('transaction belongs to a booking account', function (): void {
    $bookingAccount = BookingAccount::factory()->create();
    $transaction = Transaction::factory()->create([
        'booking_account_id' => $bookingAccount->id,
    ]);

    expect($transaction->bookingAccount)->toBeInstanceOf(BookingAccount::class)
        ->and($transaction->bookingAccount->id)->toBe($bookingAccount->id);
});

test('tax attribute is computed from gross minus net', function (): void {
    $transaction = Transaction::factory()->create([
        'amount_gross' => 11900,
        'amount_net' => 10000,
    ]);

    expect($transaction->tax)->toBe(1900);
});

test('grossForHumans returns formatted string', function (): void {
    $account = Account::factory()->create();
    $transaction = Transaction::create([
        'date' => now(),
        'label' => 'Test',
        'amount_gross' => 11900,
        'vat' => 19,
        'amount_net' => 10000,
        'account_id' => $account->id,
        'type' => TransactionType::Deposit,
        'status' => TransactionStatus::booked,
    ]);

    expect($transaction->grossForHumans())->toBe('+119,00')
        ->and($transaction->grossForHumans(false))->toBe('119,00');
});

test('grossColor returns color based on type', function (): void {
    $deposit = Transaction::factory()->create(['type' => TransactionType::Deposit]);
    $withdrawal = Transaction::factory()->create(['type' => TransactionType::Withdrawal]);

    expect($deposit->grossColor())->toBe(TransactionType::Deposit->color())
        ->and($withdrawal->grossColor())->toBe(TransactionType::Withdrawal->color());
});

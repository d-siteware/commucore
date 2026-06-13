<?php

declare(strict_types=1);

use App\Enums\AccountType;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\Transaction;

test('an account can be created via factory', function (): void {
    $account = Account::factory()->create();

    expect($account)->toBeInstanceOf(Account::class)
        ->and($account->exists)->toBeTrue();
});

test('account type is cast to enum', function (): void {
    $account = Account::factory()->create(['type' => AccountType::bank]);

    expect($account->type)->toBeInstanceOf(AccountType::class)
        ->and($account->type)->toBe(AccountType::bank);
});

test('account has transactions', function (): void {
    $account = Account::factory()->create();
    Transaction::factory()->count(3)->create(['account_id' => $account->id]);

    expect($account->transactions)->toHaveCount(3);
});

test('account has reports', function (): void {
    $account = Account::factory()->create();
    AccountReport::factory()->count(2)->create([
        'account_id' => $account->id,
    ]);

    expect($account->reports)->toHaveCount(2);
});

test('account balance starts at starting amount with no transactions', function (): void {
    $account = Account::factory()->create(['starting_amount' => 50000]);

    expect($account->accountBalance())->toBe(50000);
});

test('account balance adds deposits and subtracts withdrawals', function (): void {
    $account = Account::factory()->create(['starting_amount' => 100000]);
    Transaction::factory()->create([
        'account_id' => $account->id,
        'type' => \App\Enums\TransactionType::Deposit,
        'amount_gross' => 5000,
        'status' => \App\Enums\TransactionStatus::booked,
    ]);
    Transaction::factory()->create([
        'account_id' => $account->id,
        'type' => \App\Enums\TransactionType::Withdrawal,
        'amount_gross' => 3000,
        'status' => \App\Enums\TransactionStatus::booked,
    ]);

    expect($account->accountBalance())->toBe(102000);
});

test('account balance ignores unbooked transactions', function (): void {
    $account = Account::factory()->create(['starting_amount' => 50000]);
    Transaction::factory()->create([
        'account_id' => $account->id,
        'type' => \App\Enums\TransactionType::Deposit,
        'amount_gross' => 5000,
        'status' => \App\Enums\TransactionStatus::submitted,
    ]);

    expect($account->accountBalance())->toBe(50000);
});

test('makeCentInteger converts german formatted string to cents', function (): void {
    expect(Account::makeCentInteger('100,00'))->toBe(10000)
        ->and(Account::makeCentInteger('1.234,56'))->toBe(123456)
        ->and(Account::makeCentInteger('99'))->toBe(99)
        ->and(Account::makeCentInteger(5000))->toBe(5000);
});

test('formatedAmount converts cents to german formatted string', function (): void {
    expect(Account::formatedAmount(10000))->toBe('100,00')
        ->and(Account::formatedAmount(123456))->toBe('1.234,56')
        ->and(Account::formatedAmount(0))->toBe('0,00');
});

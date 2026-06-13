<?php

declare(strict_types=1);

use App\Enums\AccountCategory;
use App\Enums\AccountSubtype;
use App\Enums\BookingAccountArea;
use App\Models\Accounting\BookingAccount;

test('a booking account can be created via factory', function (): void {
    $account = BookingAccount::factory()->create();

    expect($account)->toBeInstanceOf(BookingAccount::class)
        ->and($account->exists)->toBeTrue();
});

test('booking account category is cast to enum', function (): void {
    $account = BookingAccount::factory()->create(['category' => AccountCategory::Income]);

    expect($account->category)->toBeInstanceOf(AccountCategory::class)
        ->and($account->category->value)->toBe('income');
});

test('booking account subtype is cast to enum when set', function (): void {
    $account = BookingAccount::factory()->create(['subtype' => AccountSubtype::Bank]);

    expect($account->subtype)->toBeInstanceOf(AccountSubtype::class)
        ->and($account->subtype->value)->toBe('bank');
});

test('booking account area is cast to enum', function (): void {
    $account = BookingAccount::factory()->create(['area' => BookingAccountArea::IDEAL]);

    expect($account->area)->toBeInstanceOf(BookingAccountArea::class)
        ->and($account->area->value)->toBe('ideal');
});

test('income factory state sets income category', function (): void {
    $account = BookingAccount::factory()->income()->create();

    expect($account->isIncomeAccount())->toBeTrue()
        ->and($account->isExpenseAccount())->toBeFalse();
});

test('expense factory state sets expense category', function (): void {
    $account = BookingAccount::factory()->expense()->create();

    expect($account->isExpenseAccount())->toBeTrue()
        ->and($account->isIncomeAccount())->toBeFalse();
});

test('bank factory state sets bank subtype', function (): void {
    $account = BookingAccount::factory()->bank()->create();

    expect($account->isPaymentAccount())->toBeTrue()
        ->and($account->subtype->value)->toBe('bank');
});

test('cash factory state sets cash subtype', function (): void {
    $account = BookingAccount::factory()->cash()->create();

    expect($account->isPaymentAccount())->toBeTrue()
        ->and($account->subtype->value)->toBe('cash');
});

test('payment accounts scope returns only bank and cash', function (): void {
    BookingAccount::factory()->bank()->create();
    BookingAccount::factory()->cash()->create();
    BookingAccount::factory()->income()->create(['subtype' => null]);

    $paymentAccounts = BookingAccount::paymentAccounts()->get();

    expect($paymentAccounts)->toHaveCount(2);
});

test('by category scope filters correctly', function (): void {
    BookingAccount::factory()->income()->create();
    BookingAccount::factory()->expense()->create();

    $incomeAccounts = BookingAccount::byCategory(AccountCategory::Income)->get();

    expect($incomeAccounts)->toHaveCount(1)
        ->and($incomeAccounts->first()->isIncomeAccount())->toBeTrue();
});

test('by area scope filters correctly', function (): void {
    BookingAccount::factory()->create(['area' => BookingAccountArea::IDEAL]);
    BookingAccount::factory()->create(['area' => BookingAccountArea::PURPOSE_OPERATION]);

    $idealAccounts = BookingAccount::byArea(BookingAccountArea::IDEAL)->get();

    expect($idealAccounts)->toHaveCount(1);
});

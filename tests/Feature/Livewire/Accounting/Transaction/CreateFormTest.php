<?php

declare(strict_types=1);

use App\Livewire\Accounting\Transaction\Create\Form;
use App\Models\Accounting\Account;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $user = User::factory()->create(['is_admin' => true]);
    Member::factory()->create(['user_id' => $user->id]);
    $this->actingAs($user);
});

test('a withdrawal transaction can be created', function (): void {
    $account = Account::factory()->create();

    Livewire::test(Form::class)
        ->set('form.label', 'Test Withdrawal')
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.amount_net', '100.00')
        ->set('form.vat', 19)
        ->set('form.amount_gross', '119.00')
        ->set('form.account_id', $account->id)
        ->set('form.type', 'Withdrawal')
        ->set('form.status', 'submitted')
        ->call('submitTransaction')
        ->assertHasNoErrors();

    $transaction = Transaction::where('label', 'Test Withdrawal')->first();
    expect($transaction)->not->toBeNull()
        ->and($transaction->type->value)->toBe('Withdrawal')
        ->and($transaction->amount_gross)->toBe(11900);
});

test('a deposit transaction can be created', function (): void {
    $account = Account::factory()->create();

    Livewire::test(Form::class)
        ->set('form.label', 'Test Deposit')
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.amount_net', '50.00')
        ->set('form.vat', 19)
        ->set('form.amount_gross', '59.50')
        ->set('form.account_id', $account->id)
        ->set('form.type', 'Deposit')
        ->set('form.status', 'submitted')
        ->call('submitTransaction')
        ->assertHasNoErrors();

    $transaction = Transaction::where('label', 'Test Deposit')->first();
    expect($transaction)->not->toBeNull()
        ->and($transaction->type->value)->toBe('Deposit');
});

test('transaction creation validates required fields', function (): void {
    Livewire::test(Form::class)
        ->call('submitTransaction')
        ->assertHasErrors([
            'form.amount_net',
            'form.amount_gross',
            'form.account_id',
        ]);
});

test('amounts are stored as cent integers', function (): void {
    $account = Account::factory()->create();

    Livewire::test(Form::class)
        ->set('form.label', 'Amount Test')
        ->set('form.date', now()->format('Y-m-d'))
        ->set('form.amount_net', '99.99')
        ->set('form.vat', 19)
        ->set('form.amount_gross', '118.99')
        ->set('form.account_id', $account->id)
        ->set('form.type', 'Withdrawal')
        ->set('form.status', 'submitted')
        ->call('submitTransaction')
        ->assertHasNoErrors();

    $transaction = Transaction::where('label', 'Amount Test')->first();
    expect($transaction->amount_gross)->toBe(11899)
        ->and($transaction->amount_net)->toBe(9999);
});

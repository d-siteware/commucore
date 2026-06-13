<?php

declare(strict_types=1);

use App\Actions\Accounting\CreateAccount;
use App\Enums\AccountType;
use App\Models\Accounting\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates an account from data', function (): void {
    $account = CreateAccount::handle([
        'name' => 'Girokonto',
        'number' => '123456',
        'institute' => 'Musterbank',
        'type' => AccountType::bank,
        'iban' => 'DE12345678901234567890',
        'bic' => 'MUSTDEBBXXX',
        'starting_amount' => '100,50',
    ]);

    expect($account)->toBeInstanceOf(Account::class)
        ->and($account->name)->toBe('Girokonto')
        ->and($account->number)->toBe('123456')
        ->and($account->institute)->toBe('Musterbank')
        ->and($account->type)->toBe(AccountType::bank)
        ->and($account->iban)->toBe('DE12345678901234567890')
        ->and($account->bic)->toBe('MUSTDEBBXXX')
        ->and($account->starting_amount)->toBe(10050); // cents
});

it('persists account to database', function (): void {
    $account = CreateAccount::handle([
        'name' => 'Sparkonto',
        'number' => '789012',
        'institute' => 'Sparkasse',
        'type' => AccountType::cash,
        'iban' => 'DE98765432109876543210',
        'bic' => 'SPKADEBBXXX',
        'starting_amount' => 0,
    ]);

    expect(Account::find($account->id))->not->toBeNull()
        ->and(Account::count())->toBe(1);
});

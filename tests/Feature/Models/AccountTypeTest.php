<?php

declare(strict_types=1);

use App\Models\Accounting\AccountType;

test('an account type can be created', function (): void {
    $accountType = new AccountType;
    $accountType->name = 'Girokonto';
    $accountType->save();

    expect($accountType->exists)->toBeTrue()
        ->and($accountType->name)->toBe('Girokonto');
});

test('multiple account types can be created', function (): void {
    foreach (['Girokonto', 'Tagesgeld', 'Festgeld'] as $name) {
        $type = new AccountType;
        $type->name = $name;
        $type->save();
    }

    expect(AccountType::count())->toBe(3);
});

<?php

declare(strict_types=1);

use App\Enums\AccountType;
use App\Models\Accounting\Account;
use App\Services\Accounting\Datev\DatevGegenkontoResolver;

describe('DatevGegenkontoResolver', function (): void {

    it('returns 920 for cash accounts', function (): void {
        $account = new Account(['type' => AccountType::cash->value]);

        expect(DatevGegenkontoResolver::resolve($account))->toBe('920');
    });

    it('returns 945 for bank accounts', function (): void {
        $account = new Account(['type' => AccountType::bank->value]);

        expect(DatevGegenkontoResolver::resolve($account))->toBe('945');
    });

    it('returns 950 for paypal accounts', function (): void {
        $account = new Account(['type' => AccountType::paypal->value]);

        expect(DatevGegenkontoResolver::resolve($account))->toBe('950');
    });

    it('throws UnexpectedValueException for unknown account type', function (): void {
        $account = new Account(['type' => 'unknown']);

        expect(fn () => DatevGegenkontoResolver::resolve($account))
            ->toThrow(\UnexpectedValueException::class);
    });

});

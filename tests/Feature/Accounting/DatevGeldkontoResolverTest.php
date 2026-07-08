<?php

declare(strict_types=1);

use App\Enums\AccountType;
use App\Models\Accounting\Account;
use App\Services\Accounting\Datev\DatevGeldkontoResolver;

describe('DatevGeldkontoResolver', function (): void {

    it('returns 16000 for cash accounts', function (): void {
        $account = new Account(['type' => AccountType::cash]);

        expect(DatevGeldkontoResolver::resolve($account))->toBe('16000');
    });

    it('returns 18000 for bank accounts', function (): void {
        $account = new Account(['type' => AccountType::bank]);

        expect(DatevGeldkontoResolver::resolve($account))->toBe('18000');
    });

    it('returns 18100 for paypal accounts', function (): void {
        $account = new Account(['type' => AccountType::paypal]);

        expect(DatevGeldkontoResolver::resolve($account))->toBe('18100');
    });

});

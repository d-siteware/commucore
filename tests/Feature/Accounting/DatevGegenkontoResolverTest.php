<?php

declare(strict_types=1);

use App\Enums\AccountType;
use App\Models\Accounting\Account;
use App\Services\Accounting\Datev\DatevGegenkontoResolver;

describe('DatevGegenkontoResolver', function (): void {

    it('returns 16000 for cash accounts', function (): void {
        $account = new Account(['type' => AccountType::cash]);

        expect(DatevGegenkontoResolver::resolve($account))->toBe('16000');
    });

    it('returns 16100 for bank accounts', function (): void {
        $account = new Account(['type' => AccountType::bank]);

        expect(DatevGegenkontoResolver::resolve($account))->toBe('16100');
    });

    it('returns 16120 for paypal accounts', function (): void {
        $account = new Account(['type' => AccountType::paypal]);

        expect(DatevGegenkontoResolver::resolve($account))->toBe('16120');
    });

});

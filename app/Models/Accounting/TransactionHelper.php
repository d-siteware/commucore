<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use App\Helpers\MoneyHelper;

final class TransactionHelper
{
    private Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function netForHumans(): string
    {
        return MoneyHelper::formatCents($this->transaction->amount_net, withSymbol: false);
    }

    public function taxForHumans(): string
    {
        return MoneyHelper::formatCents($this->transaction->tax, withSymbol: false);
    }

    public function grossForHumans(bool $withSign = true, $sign = ''): string
    {
        $amount = $this->transaction->amount_gross;

        if ($withSign) {
            $effect = $amount * $this->transaction->type->multiplier();
            $sign = $effect > 0 ? '+' : ($effect < 0 ? '-' : '');
        }

        return $sign.MoneyHelper::formatCents(abs($amount), withSymbol: false);
    }
}

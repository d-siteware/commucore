<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use App\Enums\TransactionType;

final class TransactionHelper
{
    private Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function netForHumans(): string
    {
        return \App\Helpers\MoneyHelper::formatCents($this->transaction->amount_net, withSymbol: false);
    }

    public function taxForHumans(): string
    {
        return \App\Helpers\MoneyHelper::formatCents($this->transaction->tax, withSymbol: false);
    }

    public function grossForHumans(bool $withSign = true, $sign =''): string
    {
        $amount = $this->transaction->amount_gross;

        if($withSign) {
            $sign = $this->transaction->type === TransactionType::Deposit ? '+' : '-';
        }

        return $sign.\App\Helpers\MoneyHelper::formatCents($amount, withSymbol: false);
    }
}

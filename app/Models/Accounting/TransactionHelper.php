<?php

declare(strict_types=1);

namespace App\Models\Accounting;

final class TransactionHelper
{
    protected string $komma = ',';

    protected string $tausender = '.';

    protected int $decimals = 2;

    private Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function netForHumans(): string
    {
        return number_format(($this->transaction->amount_net / 100), $this->decimals, $this->komma, $this->tausender);
    }

    public function taxForHumans(): string
    {
        return number_format(($this->transaction->tax / 100), $this->decimals, $this->komma, $this->tausender);
    }

    public function grossForHumans(): string
    {
        $amount = $this->transaction->amount_gross;
        $sign = $amount < 0 ? '-' : '+';

        return $sign.number_format(($amount / 100), $this->decimals, $this->komma, $this->tausender);
    }
}

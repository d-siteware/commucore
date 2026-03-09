<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use App\Enums\TransactionType;

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

    public function grossForHumans(bool $withSign = true, $sign =''): string
    {

        $amount = $this->transaction->amount_gross;

        if($withSign) {
            $sign = $this->transaction->type === TransactionType::Deposit ? '+' : '-';
        }

        return $sign.number_format(($amount / 100), $this->decimals, $this->komma, $this->tausender);
    }
}

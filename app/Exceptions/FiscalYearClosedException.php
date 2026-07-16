<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\Accounting\Transaction;
use RuntimeException;

/**
 * GoBD / § 146 Abs. 4 AO: Buchungen in einem geschlossenen Geschäftsjahr
 * sind unveränderbar. Korrekturen laufen ausschließlich über eine
 * Storno-Gegenbuchung im offenen Jahr.
 *
 * Die Message ist bewusst userfreundlich & übersetzt – der HandlesErrors-Trait
 * zeigt sie unverändert als Toast an.
 */
final class FiscalYearClosedException extends RuntimeException
{
    public function __construct(public readonly Transaction $transaction)
    {
        // getOriginal() beim updating-Event – der neue Wert liegt noch nicht
        // in der DB, aber $transaction->label ist bereits aufgesetzt.
        $label = $transaction->getOriginal('label') ?? $transaction->label;

        parent::__construct(__('transaction.fiscal_year_locked', [
            'label' => $label,
        ]));
    }
}

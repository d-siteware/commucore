<?php

declare(strict_types=1);

namespace App\Observers;

use App\Exceptions\FiscalYearClosedException;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;

final class TransactionObserver
{
    /**
     * Auto-Zuordnung des Geschäftsjahres beim Anlegen.
     *
     * Maßgeblich ist das Vorgangsdatum (Zufluss-/Abflussprinzip § 11 EStG),
     * Fallback ist das aktuelle Jahr in der Accounting-Timezone.
     *
     * `??=` ist entscheidend: ein explizit gesetzter Override
     * (10-Tage-Regel, § 11 Abs. 1 S. 2 EStG) gewinnt immer.
     *
     * ⚠️ QB-Writes (DB::table()->insert()) umgehen diesen Observer –
     * alle Transaction-Erzeugungen müssen über Eloquent laufen.
     */
    public function creating(Transaction $transaction): void
    {
        $year = $transaction->date?->year
            ?? now(config('commucore.accounting_timezone'))->year;

        $transaction->fiscal_year_id ??= FiscalYear::getOrCreate($year)->id;
    }

    /**
     * Schreibsperre (GoBD / § 146 Abs. 4 AO) auf Datenebene:
     * Buchungen in einem geschlossenen Geschäftsjahr sind unveränderbar.
     *
     * Bewusst FiscalYear::find() statt der (potenziell stale gecachten)
     * Relation – bei dirty fiscal_year_id würde $transaction->fiscalYear
     * sonst das alte FY liefern.
     *
     * @throws FiscalYearClosedException
     */
    public function updating(Transaction $transaction): void
    {
        // Wechsel AUS einem geschlossenen Jahr heraus verbieten
        if ($transaction->isDirty('fiscal_year_id')) {
            $original = FiscalYear::find($transaction->getOriginal('fiscal_year_id'));

            if ($original?->isClosed()) {
                throw new FiscalYearClosedException($transaction);
            }
        }

        // Buchung im (neuen wie unveränderten) geschlossenen Jahr sperren
        if (FiscalYear::find($transaction->fiscal_year_id)?->isClosed()) {
            throw new FiscalYearClosedException($transaction);
        }
    }

    /**
     * @throws FiscalYearClosedException
     */
    public function deleting(Transaction $transaction): void
    {
        if (FiscalYear::find($transaction->fiscal_year_id)?->isClosed()) {
            throw new FiscalYearClosedException($transaction);
        }
    }
}

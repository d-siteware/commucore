<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\Transaction;

final class BookingAccountObserver
{
    /**
     * Label darf nur geändert werden, wenn keine Transaktionen in
     * abgeschlossenen Geschäftsjahren existieren.
     *
     * category, area und subtype dürfen nie geändert werden, sobald
     * irgendeine Transaktion auf dieses Konto verweist (offen oder
     * geschlossen) – sie würden rückwirkend die Sphären- und
     * Kategorie-Zuordnung verschieben.
     */
    public function updating(BookingAccount $account): void
    {
        if (! $account->isDirty(['label', 'category', 'area', 'subtype'])) {
            return;
        }

        $hasAnyTransaction = Transaction::where('booking_account_id', $account->id)->exists();

        if ($account->isDirty('label')) {
            if ($hasAnyTransaction && $this->hasClosedFiscalYearTransactions($account->id)) {
                throw new \LogicException(
                    'Das Label eines Buchungskontos kann nicht geändert werden, '
                    .'sobald Transaktionen in einem abgeschlossenen Geschäftsjahr existieren.'
                );
            }
        }

        $hasStructuralChange = $account->isDirty(['category', 'area', 'subtype']);

        if ($hasStructuralChange && $hasAnyTransaction) {
            throw new \LogicException(
                'Kategorie, Sphäre und Untertyp eines Buchungskontos können nicht geändert werden, '
                .'sobald Transaktionen auf dieses Konto verweisen.'
            );
        }
    }

    public function deleting(BookingAccount $account): void
    {
        if (Transaction::where('booking_account_id', $account->id)->exists()) {
            throw new \LogicException(
                'Ein Buchungskonto kann nicht gelöscht werden, '
                .'solange Transaktionen darauf verweisen.'
            );
        }
    }

    private function hasClosedFiscalYearTransactions(int $bookingAccountId): bool
    {
        return Transaction::where('booking_account_id', $bookingAccountId)
            ->whereHas('fiscalYears', fn ($q) => $q->whereNotNull('closed_at'))
            ->exists();
    }
}

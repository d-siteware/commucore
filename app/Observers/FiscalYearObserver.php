<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Accounting\FiscalYear;

final class FiscalYearObserver
{
    public function updating(FiscalYear $fiscalYear): void
    {
        if (! $fiscalYear->isDirty('booking_account_type_id')) {
            return;
        }

        if ($fiscalYear->transactions()->exists()) {
            throw new \LogicException(
                'Der Buchungskontentyp kann nicht geändert werden, '
                .'sobald Transaktionen im Geschäftsjahr existieren.'
            );
        }
    }
}

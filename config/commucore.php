<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Accounting-Timezone
    |--------------------------------------------------------------------------
    |
    | Zentrale Zeitzone für alle buchhalterischen Jahresgrenzen:
    | - FiscalYear::getActive() (Klemmung aufs Kalenderjahr)
    | - TransactionObserver (Fallback-Jahr bei fehlendem Datum)
    | - Grenzfenster-Prüfung der 10-Tage-Regel (§ 11 EStG)
    |
    | Gilt für DE/AT/CH-Kunden identisch (CET/CEST). NICHT an Einzelstellen
    | hartkodieren – immer über diese Config beziehen.
    |
    */

    'accounting_timezone' => env('COMMUCORE_ACCOUNTING_TIMEZONE', 'Europe/Berlin'),

];

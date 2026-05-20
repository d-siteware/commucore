<?php

declare(strict_types=1);

namespace App\Services\Accounting\Datev;

use App\Enums\AccountType;
use App\Models\Accounting\Account;

/**
 * Leitet das DATEV-Gegenkonto aus dem Zahlungsmittelkonto (Account) ab.
 *
 * Bei EÜR-Buchungen ist das Gegenkonto immer das Zahlungsmittelkonto
 * (Kasse oder Bank), da keine doppelte Buchführung stattfindet.
 *
 * Mapping AccountType → SKR42-Kontonummer:
 *   cash   (Barkasse) → 16000
 *   bank   (Bank)     → 16100
 *   paypal            → 16120  (Bank 2 / PayPal)
 *
 * Hinweis: Account::$type ist in der DB als string gespeichert (kein Enum-Cast).
 * Der match arbeitet daher auf dem string-Value des Enums.
 *
 * @see SKR42BookingAccountSeeder – Konten 16000, 16100, 16120
 */
final class DatevGegenkontoResolver
{
    /**
     * Gibt die SKR42-Kontonummer des Gegenkontos zurück.
     * Führende Nullen werden entfernt (DATEV-konform).
     */
    public static function resolve(Account $account): string
    {
        $raw = match ($account->type) {
            AccountType::cash => '16000',
            AccountType::bank => '18000',
            AccountType::paypal => '18100',
        };

        // DATEV erwartet Kontonummern ohne führende Null
        return ltrim($raw, '0');
    }
}

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
 * Mapping AccountType → SKR49-Kontonummer:
 *   cash   (Barkasse) → 920
 *   bank   (Bank)     → 945
 *   paypal            → 950  (Bank 1 / PayPal – eigenes Konto)
 *
 * Hinweis: Account::$type ist in der DB als string gespeichert (kein Enum-Cast).
 * Der match arbeitet daher auf dem string-Value des Enums.
 *
 * @see SKR49BookingAccountSeeder – Konten 920, 945, 950
 */
final class DatevGegenkontoResolver
{
    /**
     * Gibt die SKR49-Kontonummer des Gegenkontos zurück.
     * Format: String ohne führende Null (DATEV-konform).
     */
    public static function resolve(Account $account): string
    {
        return match ($account->type) {
            AccountType::cash->value => '920',
            AccountType::bank->value => '945',
            AccountType::paypal->value => '950',
            default => throw new \UnexpectedValueException(
                "Unbekannter AccountType '{$account->type}' – kein Gegenkonto ableitbar."
            ),
        };
    }
}

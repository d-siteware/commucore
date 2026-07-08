<?php

declare(strict_types=1);

namespace App\Services\Accounting\Datev;

use App\Enums\AccountType;
use App\Models\Accounting\Account;

/**
 * Leitet das DATEV-Geldkonto (Feld 7 "Konto" im Buchungsstapel,
 * Kassenbuch-Konvention) aus dem Zahlungsmittelkonto (Account) ab.
 *
 * Das Soll/Haben-Kennzeichen des Buchungssatzes bezieht sich auf dieses
 * Konto: S = Geldeingang, H = Geldausgang.
 *
 * Mapping AccountType → SKR42-Kontonummer (5-stellig):
 *   cash   (Barkasse) → 16000  (Kasse)
 *   bank   (Bank)     → 18000  (Bank)
 *   paypal            → 18100  (Bank 2 / PayPal)
 *
 * Hinweis: Account::$type ist als Enum gecastet; der match arbeitet auf
 * den Enum-Cases.
 *
 * @see SKR42BookingAccountSeeder – Konten 16000, 18000, 18100
 */
final class DatevGeldkontoResolver
{
    /**
     * Gibt die SKR42-Kontonummer des Geldkontos zurück.
     * Führende Nullen werden entfernt (Kontofeld ist numerisch).
     */
    public static function resolve(Account $account): string
    {
        $raw = match ($account->type) {
            AccountType::cash => '16000',
            AccountType::bank => '18000',
            AccountType::paypal => '18100',
        };

        return ltrim($raw, '0');
    }
}

<?php

declare(strict_types=1);

namespace App\Services\Accounting\Datev;

/**
 * Mappt den MwSt-Prozentsatz (Transaction.vat) auf den DATEV BU-Schlüssel.
 *
 * Der BU-Schlüssel (Feld 9 im Buchungsstapel) ist der DATEV-Steuerschlüssel
 * und wirkt auf das Gegenkonto (Sachkonto). Er ist richtungsabhängig:
 *
 *   Einnahmen (Umsatzsteuer):   7% → "2"   19% → "3"
 *   Ausgaben  (Vorsteuer):      7% → "8"   19% → "9"
 *   0% / steuerfrei:            leer (ideeller Bereich, Spenden)
 *
 * WICHTIG (Automatikkonten): Ist das Sachkonto in DATEV als Automatikkonto
 * (AM/AV) geschlüsselt, darf KEIN BU-Schlüssel übergeben werden – die Steuer
 * wird dann über das Konto selbst gerechnet und der Import bricht bei
 * gesetztem Schlüssel ab. In dem Fall muss der Steuerberater die betroffenen
 * Konten nennen bzw. der Export ohne BU-Schlüssel abgestimmt werden.
 *
 * @see https://developer.datev.de/de/file-format/details/datev-format/format-description/booking-batch
 */
final class DatevBuKeyMapping
{
    /**
     * Gibt den DATEV BU-Schlüssel für einen MwSt-Prozentsatz zurück.
     *
     * @param  bool  $isExpense  true = Ausgabe (Vorsteuer), false = Einnahme (Umsatzsteuer)
     * @return string|null null wenn kein BU-Schlüssel benötigt wird (steuerfrei / unbekannter Satz)
     */
    public static function fromVatPercent(int $vatPercent, bool $isExpense = false): ?string
    {
        return match ($vatPercent) {
            7 => $isExpense ? '8' : '2',
            19 => $isExpense ? '9' : '3',
            default => null,
        };
    }

    /**
     * Gibt den BU-Schlüssel als String für die CSV-Ausgabe zurück.
     * Leerer String wenn kein BU-Schlüssel nötig.
     */
    public static function toCsvValue(int $vatPercent, bool $isExpense = false): string
    {
        return self::fromVatPercent($vatPercent, $isExpense) ?? '';
    }
}

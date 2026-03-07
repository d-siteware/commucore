<?php

declare(strict_types=1);

namespace App\Services\Accounting\Datev;

/**
 * Mappt den MwSt-Prozentsatz (Transaction.vat) auf den DATEV BU-Schlüssel.
 *
 * Der BU-Schlüssel ist eine DATEV-interne Codierung für die Umsatzsteuer
 * und unterscheidet sich vom reinen Prozentsatz.
 *
 * Relevante Sätze für Vereine (SKR49):
 *   0%  → leer       – steuerfrei / nicht steuerbar (ideeller Bereich, Spenden)
 *   7%  → "8"        – ermäßigter Steuersatz (Zweckbetriebe)
 *   19% → "9"        – Regelsteuersatz (wirtschaftlicher Geschäftsbetrieb)
 *
 * @see https://developer.datev.de/datev/platform/de/dtvf/formate/buchungsstapel
 */
final class DatevBuKeyMapping
{
    /**
     * Gibt den DATEV BU-Schlüssel für einen MwSt-Prozentsatz zurück.
     * Gibt null zurück wenn kein BU-Schlüssel benötigt wird (steuerfrei).
     */
    public static function fromVatPercent(int $vatPercent): ?string
    {
        return match ($vatPercent) {
            0 => null,
            7 => '8',
            19 => '9',
            default => null, // Unbekannte Sätze: leer lassen, nicht abbrechen
        };
    }

    /**
     * Gibt den BU-Schlüssel als String für die CSV-Ausgabe zurück.
     * Leerer String wenn kein BU-Schlüssel nötig.
     */
    public static function toCsvValue(int $vatPercent): string
    {
        return self::fromVatPercent($vatPercent) ?? '';
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Konkreter Untertyp eines Buchungskontos.
 *
 * Nur für Konten gesetzt, bei denen der Untertyp buchhalterisch
 * relevant ist (Zahlungsmittel, Forderungen, Verbindlichkeiten).
 * Alle anderen Konten haben subtype = null.
 *
 * Erweiterbar um FixedAsset, Equity, Prepayment etc. sobald
 * Bilanz-Reporting benötigt wird.
 */
enum AccountSubtype: string
{
    // Zahlungsmittel
    case Bank = 'bank';
    case Cash = 'cash';

    // Forderungen & Verbindlichkeiten
    case Receivable = 'receivable';
    case Payable = 'payable';

    // --- Erweiterungspunkte (noch nicht aktiv) ---
    // case FixedAsset  = 'fixed_asset';   // Anlagevermögen
    // case Equity      = 'equity';         // Eigenkapital / Rücklagen
    // case Prepayment  = 'prepayment';     // Rechnungsabgrenzung
    // case TaxAccount  = 'tax_account';    // USt-Konten

    public function label(): string
    {
        return match ($this) {
            self::Bank => __('accounting.subtype.bank'),
            self::Cash => __('accounting.subtype.cash'),
            self::Receivable => __('accounting.subtype.receivable'),
            self::Payable => __('accounting.subtype.payable'),
        };
    }

    /**
     * Ist dieser Subtype ein Zahlungsmittel?
     * Relevant für DATEV-Gegenkonto-Ableitung.
     */
    public function isPaymentMedium(): bool
    {
        return match ($this) {
            self::Bank, self::Cash => true,
            default => false,
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->toArray();
    }
}

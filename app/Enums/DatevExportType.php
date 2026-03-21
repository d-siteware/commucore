<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Export-Typen für die Buchhaltung (DATEV-Format).
 *
 * Getrennt von MemberExportType um klare Zuständigkeiten zu haben.
 */
enum DatevExportType: string
{
    /**
     * DATEV Buchungsstapel CSV (Formatversion 700).
     * Wird automatisch beim Schließen eines FiscalYear erzeugt.
     */
    case BUCHUNGSSTAPEL = 'buchungsstapel';

    /**
     * DATEV Stammdaten (Kontenbeschriftungsliste SKR49).
     * Enthält alle BookingAccounts als DATEV-kompatible CSV.
     */
    case STAMMDATEN = 'stammdaten';

    public function label(): string
    {
        return match ($this) {
            self::BUCHUNGSSTAPEL => __('accounting.export.type.buchungsstapel'),
            self::STAMMDATEN => __('accounting.export.type.stammdaten'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::BUCHUNGSSTAPEL => __('accounting.export.type.buchungsstapel_desc'),
            self::STAMMDATEN => __('accounting.export.type.stammdaten_desc'),
        };
    }

    /**
     * Dateiname für den Export.
     * Jahr wird beim Buchungsstapel als Parameter übergeben.
     */
    public function filename(int $year = 0): string
    {
        return match ($this) {
            self::BUCHUNGSSTAPEL => sprintf('EXTF_Buchungsstapel_%d.csv', $year),
            self::STAMMDATEN => 'EXTF_Kontenbeschriftungen_SKR42.csv',
        };
    }

    public function storagePath(int $year = 0): string
    {
        return match ($this) {
            self::BUCHUNGSSTAPEL => sprintf('accounting/datev/%d/%s', $year, $this->filename($year)),
            self::STAMMDATEN => sprintf('accounting/datev/%s', $this->filename()),
        };
    }
}

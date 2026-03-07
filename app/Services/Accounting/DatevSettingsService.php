<?php

declare(strict_types=1);

namespace App\Services\Accounting;

use App\Services\SettingsService;

/**
 * DATEV-Konfiguration aus der settings-Tabelle (group = 'datev').
 *
 * Standardwerte aus config/branding.php werden als Fallback verwendet,
 * bis echte DATEV-Nummern über die Admin-UI eingetragen werden.
 *
 * Keys:
 *   datev.berater_nr         – Beraternummer (4–7-stellig)
 *   datev.mandant_nr         – Mandantennummer (1–5-stellig)
 *   datev.fiscal_year_start  – Monat des WJ-Beginns (1–12)
 *   datev.konto_laenge       – Sachkontonummernlänge (4 für SKR49)
 *   datev.application_info   – Freitext für DATEV-Header
 */
final class DatevSettingsService
{
    public function __construct(
        private readonly SettingsService $settings,
    ) {}

    // ==================== Getter ====================

    public function beraterNr(): string
    {
        return (string) $this->settings->get('datev.berater_nr', '0000');
    }

    public function mandantNr(): string
    {
        return (string) $this->settings->get('datev.mandant_nr', '00000');
    }

    /**
     * Monat des Geschäftsjahresbeginns (1 = Januar).
     */
    public function fiscalYearStartMonth(): int
    {
        return (int) $this->settings->get('datev.fiscal_year_start', 1);
    }

    /**
     * Sachkontonummernlänge – für SKR49 immer 4.
     */
    public function kontoLaenge(): int
    {
        return (int) $this->settings->get('datev.konto_laenge', 4);
    }

    public function applicationInfo(): string
    {
        return (string) $this->settings->get('datev.application_info', 'CommuCore');
    }

    // ==================== Setter ====================

    public function setBeraterNr(string $value): void
    {
        $this->settings->set('datev.berater_nr', $value);
    }

    public function setMandantNr(string $value): void
    {
        $this->settings->set('datev.mandant_nr', $value);
    }

    public function setFiscalYearStartMonth(int $month): void
    {
        $this->settings->set('datev.fiscal_year_start', $month, 'integer');
    }

    public function setKontoLaenge(int $length): void
    {
        $this->settings->set('datev.konto_laenge', $length, 'integer');
    }

    public function setApplicationInfo(string $value): void
    {
        $this->settings->set('datev.application_info', $value);
    }

    // ==================== Validation ====================

    /**
     * Sind die Pflichtfelder für einen DATEV-Export gesetzt?
     * Gibt false zurück solange noch Platzhalter aktiv sind.
     */
    public function isConfigured(): bool
    {
        return $this->beraterNr() !== '0000'
            && $this->mandantNr() !== '00000';
    }

    /**
     * Alle DATEV-Settings als Array (für Livewire-Forms / Admin-UI).
     *
     * @return array{
     *     berater_nr: string,
     *     mandant_nr: string,
     *     fiscal_year_start: int,
     *     konto_laenge: int,
     *     application_info: string,
     *     is_configured: bool,
     * }
     */
    public function toArray(): array
    {
        return [
            'berater_nr' => $this->beraterNr(),
            'mandant_nr' => $this->mandantNr(),
            'fiscal_year_start' => $this->fiscalYearStartMonth(),
            'konto_laenge' => $this->kontoLaenge(),
            'application_info' => $this->applicationInfo(),
            'is_configured' => $this->isConfigured(),
        ];
    }
}

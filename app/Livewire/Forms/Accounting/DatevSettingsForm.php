<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use App\Services\Accounting\DatevSettingsService;
use Livewire\Form;

/**
 * Formular für die DATEV-Konfiguration (Beraternummer, Mandantennummer etc.).
 *
 * Die Werte landen über den DatevSettingsService in der settings-Tabelle
 * (group = 'datev') und werden im Metaheader des DATEV-Exports verwendet.
 */
final class DatevSettingsForm extends Form
{
    /**
     * Sachkontonummernlänge je Kontenrahmen.
     *
     * Aktuell wird nur SKR42 unterstützt (Seeder, Geldkonto-Mapping und
     * Demo-Daten sind SKR42-basiert). Weitere Rahmen sind als Teaser
     * vorbereitet – bei Aktivierung müssen Seeder + DatevGeldkontoResolver
     * ergänzt werden (siehe zukünftiges feature/general-booking-accounting).
     */
    private const KONTO_LAENGE_BY_SKR = [
        '42' => 5,
        '49' => 4,
        '03' => 4,
        '04' => 4,
        '14' => 4,
    ];

    public string $berater_nr = '';

    public string $mandant_nr = '';

    public int $konto_laenge = 5;

    public string $skr = '42';

    public string $application_info = 'CommuCore';

    public string $recipient_email = '';

    public function rules(): array
    {
        return [
            // DATEV: Beraternummer 1001–9999999 (4–7-stellig)
            'berater_nr' => ['required', 'integer', 'min:1001', 'max:9999999'],
            // DATEV: Mandantennummer 1–99999 (1–5-stellig)
            'mandant_nr' => ['required', 'integer', 'min:1', 'max:99999'],
            // Nur SKR42 wählbar, solange Konten ausschließlich aus dem SKR42-Seeder stammen
            'skr' => ['required', 'string', 'in:42'],
            // Header-Feld 9 "Exportiert von" (max. 25 Zeichen)
            'application_info' => ['nullable', 'string', 'max:25'],
            // E-Mail-Adresse für den DATEV-Versand (optional)
            'recipient_email' => ['nullable', 'email:rfc', 'max:255'],
        ];
    }

    public function load(): void
    {
        /** @var DatevSettingsService $datevSettings */
        $datevSettings = app(DatevSettingsService::class);

        // Platzhalter nicht ins Formular übernehmen, damit "required" greift
        $this->berater_nr = $datevSettings->isConfigured() ? $datevSettings->beraterNr() : '';
        $this->mandant_nr = $datevSettings->isConfigured() ? $datevSettings->mandantNr() : '';
        $this->skr = $datevSettings->skr();
        $this->application_info = $datevSettings->applicationInfo();
        $this->recipient_email = $datevSettings->recipientEmail();
        $this->syncKontoLaenge();
    }

    public function save(DatevSettingsService $datevSettings): void
    {
        $this->syncKontoLaenge();

        $datevSettings->setBeraterNr($this->berater_nr);
        $datevSettings->setMandantNr($this->mandant_nr);
        $datevSettings->setKontoLaenge($this->konto_laenge);
        $datevSettings->setSkr($this->skr);
        $datevSettings->setApplicationInfo($this->application_info !== '' ? $this->application_info : 'CommuCore');
        $datevSettings->setRecipientEmail($this->recipient_email);
    }

    /**
     * Die Sachkontenlänge ist kein freier Wert, sondern ergibt sich aus dem
     * gewählten Kontenrahmen (SKR42 = 5). Wird beim Laden, Speichern und
     * bei SKR-Änderung synchronisiert.
     */
    public function syncKontoLaenge(): void
    {
        $this->konto_laenge = self::KONTO_LAENGE_BY_SKR[$this->skr] ?? 5;
    }
}

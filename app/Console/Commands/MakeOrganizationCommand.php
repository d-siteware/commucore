<?php

namespace App\Console\Commands;

use App\Services\SettingsService;
use Illuminate\Console\Command;

class MakeOrganizationCommand extends Command
{
    protected $signature = 'commucore:make-organization
        {--name=             : Organisationsname}
        {--email=            : E-Mail der Organisation}
        {--web=              : Website}
        {--address=          : Adresse}
        {--zip=              : PLZ}
        {--city=             : Ort}
        {--register-id=      : Vereinsregisternummer}
        {--registered-date=  : Datum der Eintragung (Y-m-d)}
        {--court=            : Registergericht}
        {--tax-id=           : Steuernummer}
        {--vat-id=           : USt-ID}
        {--fiscal-year=      : Geschäftsjahresbeginn (Jahr)}
        {--locales=          : Aktive Sprachen, kommagetrennt (z.B. de,en)}
        {--mark-onboarded    : Setzt onboarded_at in Settings}';

    protected $description = 'Setzt Organisations-Settings in einer Instanz';

    public function handle(): int
    {
        $settings = app(SettingsService::class);

        $map = [
            'organization.name'            => $this->option('name'),
            'organization.email'           => $this->option('email'),
            'organization.web'             => $this->option('web'),
            'organization.address'         => $this->option('address'),
            'organization.zip'             => $this->option('zip'),
            'organization.city'            => $this->option('city'),
            'organization.register_id'     => $this->option('register-id'),
            'organization.registered_date' => $this->option('registered-date'),
            'organization.court'           => $this->option('court'),
            'organization.tax_id'          => $this->option('tax-id'),
            'organization.vat_id'          => $this->option('vat-id'),
        ];

        // Nur Werte schreiben die auch übergeben wurden
        foreach ($map as $key => $value) {
            if ($value !== null) {
                $settings->set($key, $value);
                $this->components->info("Gesetzt: {$key} = {$value}");
            }
        }

        // Geschäftsjahr
        if ($this->option('fiscal-year')) {
            $settings->set('accounting.fiscal_year', (int) $this->option('fiscal-year'));
            $this->components->info("Gesetzt: accounting.fiscal_year = " . $this->option('fiscal-year'));
        }

        // Sprachen
        if ($this->option('locales')) {
            $locales = array_map('trim', explode(',', $this->option('locales')));
            $settings->set('locales.active', json_encode($locales));
            $this->components->info("Gesetzt: locales.active = " . implode(', ', $locales));
        }

        // Onboarding abgeschlossen
        if ($this->option('mark-onboarded')) {
            $settings->set('organization.onboarded_at', now()->toIso8601String());
            $this->components->info("Gesetzt: organization.onboarded_at = " . now()->toIso8601String());
        }

        $this->components->info('Organisation erfolgreich konfiguriert.');
        return 0;
    }
}
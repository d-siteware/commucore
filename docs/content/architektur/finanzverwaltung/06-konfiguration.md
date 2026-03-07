# Konfiguration – DATEV-Einstellungen

## Überblick

Die DATEV-Konfiguration wird über den `DatevSettingsService` verwaltet. Die Werte werden in der `settings`-Tabelle unter der Gruppe `datev` gespeichert und sind über die Admin-UI pflegbar.

---

## Einstellungen

| Key | Typ | Standard | Bedeutung |
|-----|-----|---------|-----------|
| `datev.berater_nr` | `string` | `0000` | DATEV-Beraternummer (vom Steuerberater) |
| `datev.mandant_nr` | `string` | `00000` | DATEV-Mandantennummer (vom Steuerberater) |
| `datev.fiscal_year_start` | `integer` | `1` | Monat des Geschäftsjahresbeginns (1 = Januar) |
| `datev.konto_laenge` | `integer` | `4` | Sachkontonummernlänge (SKR49 = immer 4) |
| `datev.application_info` | `string` | `CommuCore` | Freitext im DATEV-Header |

---

## Erstkonfiguration

Beim ersten `commucore:install` werden Platzhalter angelegt:

```bash
php artisan db:seed --class=DatevSettingsSeeder
```

Danach müssen Beraternummer und Mandantennummer über **Einstellungen → DATEV** eingetragen werden.

---

## Konfigurationsstatus prüfen

```php
$service = app(\App\Services\Accounting\DatevSettingsService::class);

if (! $service->isConfigured()) {
    // Platzhalter noch aktiv – Export möglich, aber nicht DATEV-ready
}
```

`isConfigured()` gibt `false` zurück solange `berater_nr === '0000'` oder `mandant_nr === '00000'`.

---

## Programmatische Nutzung

```php
use App\Services\Accounting\DatevSettingsService;

$settings = app(DatevSettingsService::class);

// Lesen
$settings->beraterNr();              // string
$settings->mandantNr();              // string
$settings->fiscalYearStartMonth();   // int (1–12)
$settings->kontoLaenge();            // int
$settings->applicationInfo();        // string

// Schreiben
$settings->setBeraterNr('1234567');
$settings->setMandantNr('12345');
$settings->setFiscalYearStartMonth(1);

// Als Array (für Livewire-Forms)
$settings->toArray();
// [
//   'berater_nr'        => '1234567',
//   'mandant_nr'        => '12345',
//   'fiscal_year_start' => 1,
//   'konto_laenge'      => 4,
//   'application_info'  => 'CommuCore',
//   'is_configured'     => true,
// ]
```

---

## Service Provider Registrierung

In `AppServiceProvider::register()`:

```php
use App\Services\Accounting\DatevSettingsService;
use App\Services\Accounting\Datev\DatevExportService;
use App\Services\SettingsService;

$this->app->singleton(DatevSettingsService::class, fn ($app) =>
    new DatevSettingsService($app->make(SettingsService::class))
);

$this->app->singleton(DatevExportService::class, fn ($app) =>
    new DatevExportService($app->make(DatevSettingsService::class))
);
```

---

## Observer Registrierung

In `AppServiceProvider::boot()` oder einem dedizierten `EventServiceProvider`:

```php
use App\Models\Accounting\FiscalYear;
use App\Observers\FiscalYearObserver;

FiscalYear::observe(FiscalYearObserver::class);
```

---

## Caching

Alle Settings werden über den `SettingsService` mit `Cache::rememberForever()` gecacht. Nach einer Änderung wird der Cache für den betroffenen Key automatisch invalidiert.

Bei Problemen kann der gesamte Cache geleert werden:

```bash
php artisan cache:clear
```
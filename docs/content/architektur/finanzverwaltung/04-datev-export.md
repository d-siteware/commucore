# DATEV Export

## Überblick

CommuCore exportiert Buchungsdaten im **DATEV-Format (EXTF) Buchungsstapel** (Hauptversion 700, Formatversion 12). Der Export wird automatisch beim Abschluss eines Geschäftsjahres erzeugt; zusätzlich können geprüfte Monatsberichte einzeln exportiert werden.

Referenz: [DATEV Developer Portal – DATEV-Format](https://developer.datev.de/de/file-format/details/datev-format/getting-started)

---

## Voraussetzungen

Vor dem ersten Export müssen folgende Werte in den Einstellungen hinterlegt sein:

- **Beraternummer** – vom DATEV-Steuerberater, 4–7-stellig
- **Mandantennummer** – vom DATEV-Steuerberater, 1–5-stellig

Solange Platzhalter aktiv sind (`0000` / `00000`), wird der Export trotzdem erzeugt – mit einem Log-Warning. Die Datei ist dann nicht für den echten DATEV-Import geeignet.

Weitere Settings (Defaults passen für SKR42):

| Key | Default | Bedeutung |
|-----|---------|-----------|
| `datev.konto_laenge` | `5` | Sachkontonummernlänge (SKR42 = 5-stellig). **Achtung:** Bei zu kleinem Wert interpretiert DATEV die Konten als Personenkonten |
| `datev.skr` | `42` | Sachkontenrahmen (Header-Feld 27) |
| `datev.application_info` | `CommuCore` | „Exportiert von" im Header |

---

## Dateiformat

### Speicherort

```
storage/app/private/accounting/datev/{Jahr}/EXTF_Buchungsstapel_{Jahr}.csv       (Geschäftsjahr)
storage/app/private/datev/EXTF_Buchungsstapel_{Y-m}_{Kontoname}.csv              (Monatsbericht)
```

Das Dateinamens-Präfix `EXTF_` ist von DATEV vorgeschrieben.

### Aufbau

```
Zeile 1: DATEV-Metaheader (31 Felder, Semikolon-getrennt)
Zeile 2: Spaltenüberschriften (125 Felder)
Zeile 3+: Buchungsdatensätze (je 125 Felder)
```

- Trennzeichen: **Semikolon** (`;`)
- Encoding: **Windows-1252 (CP1252)** – DATEV-Default für den Import (UTF-8 nur mit BOM und nur bei manuellem Import)
- Zeilenende: **CRLF** (`\r\n`), auch nach der letzten Zeile
- Textfelder in Anführungszeichen (`"…"`), innere Anführungszeichen verdoppelt

---

## Buchungsdatensatz – Kassenbuch-Konvention

Der Export folgt der Kassenbuch-Konvention: Das **Konto (Feld 7) ist das Geldkonto** (Kasse/Bank/PayPal), das **Gegenkonto (Feld 8) das SKR42-Sachkonto** (BookingAccount). Das Soll/Haben-Kennzeichen (Feld 2) bezieht sich lt. Spezifikation auf das Konto in Feld 7:

- `S` = Geldeingang (Kasse/Bank im Soll)
- `H` = Geldausgang

| Feld | Inhalt | Beispiel |
|------|--------|---------|
| 1 – Umsatz | Bruttobetrag, **immer positiv**, Komma als Dezimaltrennzeichen | `11,96` |
| 2 – Soll/Haben | Saldo-Wirkung auf das Geldkonto; Storno-Gegenbuchungen (negativer Betrag) drehen das Kennzeichen | `S` |
| 7 – Konto | Geldkonto aus `Account.type` (siehe unten) | `18000` |
| 8 – Gegenkonto | SKR42-Sachkonto (`BookingAccount.number`, numerisch) | `21100` |
| 9 – BU-Schlüssel | DATEV-Steuerschlüssel, richtungsabhängig (siehe unten) | `3` |
| 10 – Belegdatum | Tag und Monat (TTMM) | `0103` |
| 11 – Belegfeld 1 | Externe Referenz (max. 36 Zeichen; erlaubt: `a-z A-Z 0-9 $ & % * + - /`) | `RE-2025-001` |
| 12 – Belegfeld 2 | Interne Transaction-ID | `42` |
| 14 – Buchungstext | `Transaction.label` (max. 60 Zeichen) | `Mitgliedsbeitrag März 2025` |
| 37 – KOST1 | Steuerliche Sphäre (1 = ideell, 2 = Vermögensverwaltung, 3 = Zweckbetrieb, 4 = wirtschaftlicher Geschäftsbetrieb) | `1` |

Alle übrigen der 125 Felder bleiben leer.

---

## BU-Schlüssel Mapping (richtungsabhängig)

| MwSt-Satz | Einnahme (USt) | Ausgabe (Vorsteuer) |
|-----------|----------------|---------------------|
| 0 % | *(leer)* | *(leer)* |
| 7 % | `2` | `8` |
| 19 % | `3` | `9` |

> **Achtung Automatikkonten:** Ist ein Sachkonto in DATEV als Automatikkonto (AM/AV) geschlüsselt, darf **kein** BU-Schlüssel übergeben werden, sonst bricht der Import ab. Betroffene Konten mit dem Steuerberater abstimmen.

---

## Geldkonto-Ableitung (Feld 7)

Das Geldkonto wird automatisch aus `Account.type` abgeleitet (`DatevGeldkontoResolver`):

| Account.type | SKR42-Konto | Bezeichnung |
|-------------|-------------|-------------|
| `cash` | 16000 | Kasse |
| `bank` | 18000 | Bank |
| `paypal` | 18100 | Bank 2 (PayPal) |

---

## Ausgeschlossene Transaktionen

Folgende Transaktionen werden **nicht** exportiert:

- `TransactionType::Transfer` (Umbuchungen, multiplier = 0)
- Transaktionen mit `booking_account_id = null`
- Transaktionen mit `status != booked`
- Transaktionen mit Betrag 0

Übersprungene Transaktionen ohne `booking_account_id` werden als Warning geloggt.

**Stornos:** Storno-Gegenbuchungen (negativer Betrag, gleicher Typ wie das Original) werden mit positivem Umsatz und gedrehtem Soll/Haben-Kennzeichen exportiert – Original und Gegenbuchung neutralisieren sich in DATEV.

---

## Validierung

DATEV stellt das offizielle **DATEV-Format-Prüfprogramm** (`DatevFormatPruefProgramm.exe`) bereit:

- Download: [developer.datev.de → DATEV-Format → Tools](https://developer.datev.de/de/file-format/details/datev-format/tools) (kostenloser Developer-Account)
- Prüft Header, Feldformate, Feldlängen und Zeichensatz
- **Nur Windows-GUI, kein CLI** – nicht CI-automatisierbar

Wichtige Hinweise von DATEV:

- Das Prüfprogramm findet nur *technische* Fehler; die vollständige Validierung erfolgt erst beim **Testimport in DATEV Rechnungswesen** (der Steuerberater kann eine Testdatenbank verwenden)
- Der Buchungsdatenservice (Online-API) validiert nur den Header – eine erfolgreiche Übertragung bedeutet nicht, dass die Datei spezifikationskonform ist

---

## Automatischer Export (Observer)

```php
// Beim Jahresabschluss wird der Export automatisch ausgelöst:
$fiscalYear->update(['closed_at' => now(), 'closed_by' => auth()->id()]);
// → FiscalYearObserver → DatevExportService::export()
```

Ein fehlgeschlagener Export verhindert den Jahresabschluss nicht.

---

## Manueller Export

```php
$service = app(\App\Services\Accounting\Datev\DatevExportService::class);
$path = $service->export($fiscalYear);          // FiscalYear muss geschlossen sein
$path = $service->exportForReport($report);     // Bericht muss Status audited haben
```

---

## Fehlerbehandlung

| Fehler | Verhalten |
|--------|-----------|
| FiscalYear noch offen | `RuntimeException` – Export abgebrochen |
| Bericht nicht geprüft | `RuntimeException` – Export abgebrochen |
| DATEV nicht konfiguriert (Platzhalter) | Log-Warning – Export wird trotzdem erzeugt |
| Transaktion ohne `booking_account_id` | Log-Warning – Transaktion übersprungen |
| Unbekannter `Account.type` | `UnexpectedValueException` |

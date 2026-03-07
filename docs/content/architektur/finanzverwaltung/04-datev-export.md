# DATEV Export

## Überblick

CommuCore exportiert Buchungsdaten im **DATEV EXTF Buchungsstapel**-Format (Formatversion 700, Datensatzversion 12). Der Export wird automatisch beim Abschluss eines Geschäftsjahres erzeugt.

---

## Voraussetzungen

Vor dem ersten Export müssen folgende Werte in den Einstellungen hinterlegt sein:

- **Beraternummer** – vom DATEV-Steuerberater, 4–7-stellig
- **Mandantennummer** – vom DATEV-Steuerberater, 1–5-stellig

Solange Platzhalter aktiv sind (`0000` / `00000`), wird der Export trotzdem erzeugt – mit einem Log-Warning. Die Datei ist dann nicht für den echten DATEV-Import geeignet.

---

## Dateiformat

### Speicherort

```
storage/app/private/accounting/datev/{Jahr}/EXTF_Buchungsstapel_{Jahr}.csv
```

### Aufbau

```
Zeile 1: DATEV-Metaheader (28 Felder, Semikolon-getrennt)
Zeile 2: Spaltenüberschriften (64 Felder)
Zeile 3+: Buchungsdatensätze
```

Trennzeichen: **Semikolon** (`;`)
Encoding: **UTF-8** (DATEV akzeptiert UTF-8 ab 2019)
Zeilenende: **CRLF** (`\r\n`)

---

## Buchungsdatensatz – relevante Felder

| Feld | Inhalt | Beispiel |
|------|--------|---------|
| Umsatz | Bruttobetrag in EUR, Komma als Dezimaltrennzeichen | `11,96` |
| Soll/Haben | `S` = Einnahme (Deposit), `H` = Ausgabe (Withdrawal/Reversal) | `S` |
| Konto | SKR49-Kontonummer ohne führende Null | `2110` |
| Gegenkonto | Zahlungsmittelkonto (aus Account.type abgeleitet) | `945` |
| BU-Schlüssel | DATEV-Umsatzsteuercode | `9` (19%), `8` (7%), leer (0%) |
| Belegdatum | Tag und Monat (TTMM) | `0103` |
| Belegfeld 1 | Externe Referenz (max. 36 Zeichen, alphanumerisch) | `RE-2025-001` |
| Belegfeld 2 | Interne Transaction-ID | `42` |
| Buchungstext | `Transaction.label` (max. 60 Zeichen) | `Mitgliedsbeitrag März 2025` |
| KOST1 | Steuerliche Sphäre (`BookingAccountArea.value`) | `ideal` |

---

## BU-Schlüssel Mapping

| MwSt-Satz | BU-Schlüssel | Verwendung |
|-----------|-------------|------------|
| 0% | *(leer)* | Ideeller Bereich, Spenden, steuerfreie Einnahmen |
| 7% | `8` | Ermäßigter Steuersatz (Zweckbetriebe) |
| 19% | `9` | Regelsteuersatz (wirtschaftlicher Geschäftsbetrieb) |

---

## Gegenkonto-Ableitung

Das Gegenkonto wird automatisch aus `Account.type` abgeleitet:

| Account.type | SKR49-Gegenkonto | Bezeichnung |
|-------------|-----------------|-------------|
| `cash` | 920 | Kasse |
| `bank` | 945 | Bank |
| `paypal` | 950 | Bank 1 (PayPal) |

---

## Ausgeschlossene Transaktionen

Folgende Transaktionen werden **nicht** exportiert:

- `TransactionType::Transfer` (Umbuchungen, multiplier = 0)
- Transaktionen mit `booking_account_id = null`
- Transaktionen mit `status != booked`

Übersprungene Transaktionen ohne `booking_account_id` werden als Warning geloggt.

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
$path = $service->export($fiscalYear); // FiscalYear muss geschlossen sein
```

---

## Fehlerbehandlung

| Fehler | Verhalten |
|--------|-----------|
| FiscalYear noch offen | `RuntimeException` – Export abgebrochen |
| DATEV nicht konfiguriert (Platzhalter) | Log-Warning – Export wird trotzdem erzeugt |
| Transaktion ohne `booking_account_id` | Log-Warning – Transaktion übersprungen |
| Unbekannter `Account.type` | `UnexpectedValueException` |
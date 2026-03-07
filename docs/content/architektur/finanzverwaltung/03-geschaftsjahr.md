# Geschäftsjahr (FiscalYear)

## Überblick

Das `FiscalYear`-Model verwaltet den Lebenszyklus eines Geschäftsjahres. Es dient als Klammer für alle Transaktionen eines Jahres und steuert den Lock-Mechanismus, der Buchungen vor nachträglicher Änderung schützt.

---

## Lebenszyklus

```
Eröffnen          Buchen             Abschließen
    │                 │                   │
    ▼                 ▼                   ▼
opened_at        Transaktionen       closed_at gesetzt
gesetzt          werden zugeordnet   → FiscalYearObserver
                 (fiscal_year_       → DATEV-Export wird
                  transactions)        automatisch erzeugt
```

---

## Modellstruktur

| Feld | Typ | Bedeutung |
|------|-----|-----------|
| `year` | `int` | Kalenderjahr (z.B. 2025) |
| `opened_at` | `datetime` | Zeitpunkt der Eröffnung |
| `closed_at` | `datetime\|null` | Zeitpunkt des Abschlusses – null = offen |
| `opened_by` | `int\|null` | User-ID des Eröffnenden |
| `closed_by` | `int\|null` | User-ID des Abschließenden |

---

## Lock-Mechanismus

Transaktionen werden über die Pivot-Tabelle `fiscal_year_transactions` einem Geschäftsjahr zugeordnet. Das Pivot-Model `FiscalYearTransaction` speichert zusätzlich `locked_at`.

Eine Transaktion gilt als **gesperrt** wenn sie in `fiscal_year_transactions` für das aktuelle Jahr eingetragen ist. Gesperrte Transaktionen können nicht mehr bearbeitet oder gelöscht werden.

```php
// Prüfen ob eine Transaktion gesperrt ist
$transaction->isLockedInFiscalYear(2025); // bool

// Gesperrte Transaktionen laden
Transaction::query()->lockedInYear(2025)->get();

// Ungesperrte Transaktionen laden
Transaction::query()->unlocked(2025)->get();
```

---

## Hilfsmethoden

```php
// Aktives (offenes) Geschäftsjahr holen
FiscalYear::getActive(); // ?FiscalYear

// Geschäftsjahr aus Session holen
FiscalYear::getCurrent(); // ?FiscalYear

// Geschäftsjahr holen oder anlegen
FiscalYear::getOrCreate(2025, $userId); // FiscalYear

// Status prüfen
$fiscalYear->isOpen();   // bool
$fiscalYear->isClosed(); // bool

// Saldo des Jahres
$fiscalYear->balance(); // int (Cent)
```

---

## Jahresabschluss und DATEV-Export

Beim Setzen von `closed_at` reagiert der `FiscalYearObserver` automatisch:

```php
// Jahresabschluss auslösen
$fiscalYear->update([
    'closed_at' => now(),
    'closed_by' => auth()->id(),
]);
// → FiscalYearObserver::updated() wird aufgerufen
// → DatevExportService::export($fiscalYear) wird ausgeführt
// → CSV wird unter storage/app/private/accounting/datev/{Jahr}/ gespeichert
```

Der Export-Fehler bricht den Jahresabschluss **nicht** ab – Fehler werden geloggt, der Abschluss bleibt gültig.

---

## Factory (Tests)

```php
// Offenes Geschäftsjahr
FiscalYear::factory()->forYear(2025)->open()->create();

// Abgeschlossenes Geschäftsjahr
FiscalYear::factory()->forYear(2025)->closed()->create();
```
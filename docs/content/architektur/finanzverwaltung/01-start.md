# Finanzverwaltung – Architekturüberblick

## Einleitung

Die Finanzverwaltung von CommuCore ist auf die Anforderungen gemeinnütziger Vereine nach **SKR49** ausgerichtet. Sie unterstützt die Einnahmen-Überschuss-Rechnung (EÜR) mit vier steuerlichen Sphären und ermöglicht den Export von Buchungsdaten im **DATEV-Format**.

---

## Steuerliche Sphären (`BookingAccountArea`)

Gemeinnützige Vereine unterliegen dem deutschen Steuerrecht mit vier voneinander abgegrenzten Bereichen:

| Sphäre | Enum-Value | Bedeutung |
|--------|-----------|-----------|
| Ideeller Bereich | `ideal` | Mitgliedsbeiträge, Spenden, Zuschüsse |
| Vermögensverwaltung | `asset_management` | Mieteinnahmen, Zinserträge |
| Zweckbetrieb | `purpose_operation` | Sportveranstaltungen, Kursgebühren |
| Wirtschaftlicher Geschäftsbetrieb | `economic_business` | Gaststätte, Werbung, steuerpflichtige Einnahmen |

Jedes `BookingAccount` ist genau einer Sphäre zugeordnet. Diese Zuordnung fließt als **KOST1-Wert** in den DATEV-Export ein.

---

## Datenmodell

```
Account                          BookingAccount
──────────────────────           ──────────────────────────────
id                               id
name                             number        (SKR49-Kontonummer)
type  (cash|bank|paypal)         label
                                 category      (AccountCategory)
                                 subtype       (AccountSubtype|null)
                                 area          (BookingAccountArea)
        │                                │
        └──────────┐  ┌──────────────────┘
                   ▼  ▼
              Transaction
              ──────────────────────────────
              date
              label
              reference
              amount_gross   (Cent, Brutto)
              vat            (MwSt-Prozentsatz)
              amount_net     (Cent, Netto)
              tax            (berechnet: gross - net)
              type           (TransactionType)
              status         (TransactionStatus)
              account_id
              booking_account_id
                   │
                   ▼
         FiscalYearTransaction  (Pivot)
                   │
                   ▼
              FiscalYear
              ──────────────────────────────
              year
              opened_at
              closed_at
              opened_by
              closed_by
```

### Wichtige Designentscheidungen

**`tax` ist kein Datenbankfeld** – der Steuerbetrag wird als berechnetes Accessor-Attribut aus `amount_gross - amount_net` abgeleitet. Damit werden Rundungsdifferenzen vermieden und `amount_gross` bleibt die einzige Quelle der Wahrheit.

**`BookingAccountType` wurde entfernt** – der frühere Enum vermischte drei Konzepte (Buchungsrichtung, Kontotyp, Untertyp). Er wurde durch `AccountCategory` + `AccountSubtype` ersetzt.

**`booking_account_id` ist nullable** – Transaktionen ohne SKR49-Zuordnung sind technisch möglich, werden aber vom DATEV-Export ausgeschlossen.

---

## Service-Architektur

```
App\Services\Accounting\
│
├── DatevSettingsService          DATEV-Konfiguration (Beraternr., Mandantennr.)
│
└── Datev\
    ├── DatevBuKeyMapping         MwSt% → DATEV BU-Schlüssel
    ├── DatevGegenkontoResolver   Account.type → SKR49-Gegenkonto
    └── DatevExportService        Generiert DATEV Buchungsstapel CSV

App\Observers\
└── FiscalYearObserver            Triggert Export beim Jahresabschluss
```

---

## Verzeichnisstruktur (Storage)

Exportierte DATEV-Dateien werden unter `storage/app/private/` abgelegt:

```
accounting/
└── datev/
    └── {Jahr}/
        └── EXTF_Buchungsstapel_{Jahr}.csv
```

---

## Abhängigkeiten

| Komponente | Abhängigkeit |
|-----------|-------------|
| `DatevExportService` | `DatevSettingsService`, `DatevBuKeyMapping`, `DatevGegenkontoResolver` |
| `FiscalYearObserver` | `DatevExportService` |
| `BookingAccount` | `AccountCategory`, `AccountSubtype`, `BookingAccountArea` |
| `Transaction` | `BookingAccount`, `Account`, `FiscalYear` |
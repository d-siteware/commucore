# Einnahmen-Überschuss-Rechnung (EÜR)

## Überblick

Gemeinnützige Vereine sind in Deutschland zur **Einnahmen-Überschuss-Rechnung** verpflichtet, sofern sie nicht der Buchführungspflicht nach § 141 AO unterliegen. CommuCore bildet die EÜR über die vier steuerlichen Sphären ab.

---

## Grundprinzip

Bei der EÜR gilt das **Zufluss-/Abflussprinzip**:

- Einnahmen werden im Jahr des Zahlungseingangs erfasst (`Transaction.date`)
- Ausgaben werden im Jahr der Zahlung erfasst
- Keine Bilanzierung, keine Abgrenzungen

Maßgeblich ist `Transaction.amount_gross` (Bruttobetrag in Cent).

---

## Betragsberechnung

Alle Beträge werden intern in **Cent als Integer** gespeichert:

| Feld | Bedeutung | Beispiel |
|------|-----------|---------|
| `amount_gross` | Bruttobetrag (Quelle der Wahrheit) | `11900` (= 119,00 €) |
| `vat` | MwSt-Prozentsatz | `19` |
| `amount_net` | Nettobetrag | `10000` (= 100,00 €) |
| `tax` | Steuerbetrag *(berechnet)* | `1900` (= 19,00 €) |

`tax` ist kein Datenbankfeld – es wird als Accessor berechnet:

```php
public function getTaxAttribute(): int
{
    return $this->amount_gross - $this->amount_net;
}
```

---

## Sphären und ihre Buchungskonten

### Ideeller Bereich
Steuerfreie Tätigkeiten im Rahmen des Vereinszwecks.

**Typische Einnahmen:** Mitgliedsbeiträge (2110–2170), Zuschüsse (2300–2303), Spenden (3221–3230)
**Typische Ausgaben:** Personalkosten (2550–2560), Raummiete (2661), Vereinsverwaltung (2700–2900)

### Vermögensverwaltung
Verwaltung des Vereinsvermögens, in der Regel steuerfrei.

**Typische Einnahmen:** Miet-/Pachteinnahmen (4110–4120), Zinserträge (4130–4160)
**Typische Ausgaben:** Abschreibungen (4510), Instandhaltung (4520), Bankgebühren (4560)

### Zweckbetrieb
Direkte Erfüllung des steuerbegünstigten Zwecks, teilweise steuerpflichtig (7% MwSt).

**Typische Einnahmen:** Eintrittsgelder Sport (5010), Startgelder (5020), Kursgebühren (5030)
**Typische Ausgaben:** Sportbedarf (5540), Veranstaltungskosten (5550), Schiedsrichter (5570)

### Wirtschaftlicher Geschäftsbetrieb
Steuerpflichtige wirtschaftliche Tätigkeit (19% MwSt, Körperschaft- und Gewerbesteuer ab Freibetrag).

**Typische Einnahmen:** Werbung (7020–7040), Gaststätte (8010), Warenverkauf (8050)
**Typische Ausgaben:** Wareneinkauf (8530–8540), Werbekosten (8570), Körperschaftsteuer (8580)

---

## Sphärentrennung bei Abfragen

```php
use App\Enums\BookingAccountArea;
use App\Enums\AccountCategory;

// Einnahmen ideeller Bereich
Transaction::query()
    ->whereHas('bookingAccount', function ($q): void {
        $q->where('area', BookingAccountArea::IDEAL->value)
          ->where('category', AccountCategory::Income->value);
    })
    ->whereYearEquals(2025)
    ->sum('amount_gross');

// Ausgaben wirtschaftlicher Geschäftsbetrieb
Transaction::query()
    ->whereHas('bookingAccount', function ($q): void {
        $q->where('area', BookingAccountArea::ECONOMIC_BUSINESS->value)
          ->where('category', AccountCategory::Expense->value);
    })
    ->whereYearEquals(2025)
    ->sum('amount_gross');
```

---

## Jahresüberschuss/-fehlbetrag

```
Überschuss = Σ Einnahmen (alle Sphären) - Σ Ausgaben (alle Sphären)
```

Der Saldo pro Sphäre ist buchhalterisch relevant für die steuerliche Einordnung:

- **Ideeller Bereich + Vermögensverwaltung:** Überschuss ist in der Regel steuerfrei
- **Zweckbetrieb:** Überschuss bis zum Freibetrag steuerfrei
- **Wirtschaftlicher Geschäftsbetrieb:** Überschuss über 45.000 € (Freibetrag) ist körperschaft- und gewerbesteuerpflichtig

---

## Hinweis zur steuerlichen Beratung

CommuCore unterstützt die **technische Erfassung** von Buchungen nach EÜR-Grundsätzen. Die steuerrechtliche Einordnung und Jahresabschlusserstellung obliegt einem qualifizierten Steuerberater.
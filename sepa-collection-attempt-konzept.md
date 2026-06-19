# SEPA-Modul: Umbau auf `SepaCollectionAttempt`

Arbeitsauftrag für den Branch `feature/SEPA-module` (CommuCore). Ziel: eine
`Transaction` darf nur noch entstehen, wenn Geld bestätigt eingegangen ist.
Der Weg bis dahin (Mandat vorhanden → XML erzeugt → bei der Bank eingereicht →
bestätigt oder zurückgegeben) wird über eine neue, eigenständige Entität
`SepaCollectionAttempt` abgebildet, die **nicht** Teil der Buchhaltung ist.

## Leitprinzip

Eine Zeile in `sepa_collection_attempts` ist ein *Versuch*, kein Beleg. Eine
`Transaction` entsteht erst, wenn der Versuch bestätigt wurde. Auswertungen
über `Transaction`/`MemberTransaction` müssen dadurch nirgendwo mehr zwischen
SEPA und anderen Zahlarten unterscheiden – ist eine Transaction da, ist sie
real, genau wie bei Bar oder Überweisung.

---

## 1. Was unverändert bleibt (nur mit Phase-1-Fixes)

- `SepaMandate` (Model, Migration, Factory) – Mandatsverwaltung ändert sich
  durch diesen Umbau nicht.
- `SepaMandateType`, `SepaMandateStatus` – bleiben.
- `SepaMandateService` – bleibt, `markAsUsed()` wird aber künftig vom neuen
  `SepaCollectionService` aufgerufen, nicht mehr von `SepaDirectDebitService`.
- `SepaXmlValidator` – bleibt unverändert (reine Schema-Prüfung).
- `EbicsService` – bleibt, mit Fix: `uploadXml()` muss den tatsächlich
  konfigurierten `painFormat` übergeben bekommen statt den Default
  `pain.008.001.02` zu verwenden.
- `SepaMandatePdf` – bleibt, mit Fix: der 8-Wochen-Erstattungstext darf bei
  `SepaMandateType::B2b` nicht ausgegeben werden.
- `SepaSettingsService` / `SepaSettingsForm` / `config/sepa.php` / lang-Dateien
  – bleiben.
- `Member/SepaMandate/Manage.php` + Blade – Mandatsverwaltung am Mitglied
  bleibt strukturell, nur `exportSingleSepaXml()` ruft künftig
  `SepaCollectionService` auf (siehe unten).

---

## 2. Neue Migration: `sepa_collection_attempts`

```php
Schema::create('sepa_collection_attempts', function (Blueprint $table) {
    $table->id();
    $table->foreignIdFor(Member::class)->constrained()->cascadeOnDelete();
    $table->foreignIdFor(SepaMandate::class)->nullable()->constrained()->nullOnDelete();
    $table->integer('amount'); // Cent, eingefroren zum Zeitpunkt der Erzeugung
    $table->integer('fee_year');
    $table->string('remittance_information');
    $table->string('end_to_end_id');
    $table->date('due_date');
    $table->string('sequence_type', 10); // FRST | RCUR, eingefroren bei Erzeugung
    $table->string('batch_reference')->nullable(); // MsgId aus der XML, zur Gruppierung
    $table->string('status', 20)->default('submitted');
    $table->timestamp('resolved_at')->nullable();
    $table->string('return_reason')->nullable();
    $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index(['member_id', 'fee_year']);
    $table->index('batch_reference');
    $table->index('status');
});
```

`created_at` übernimmt die Rolle von „eingereicht am" – ein separates
`submitted_at` wäre redundant, da beide Zeitpunkte hier immer identisch sind.

## 3. Enums

```php
enum SepaCollectionAttemptStatus: string
{
    case Submitted = 'submitted';
    case Confirmed = 'confirmed';
    case Returned = 'returned';
}
```

Optional, aber empfohlen für Typsicherheit statt rohem String:

```php
enum SepaSequenceType: string
{
    case Frst = 'FRST';
    case Rcur = 'RCUR';
}
```

## 4. Model `SepaCollectionAttempt`

```php
final class SepaCollectionAttempt extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'due_date' => 'date',
        'resolved_at' => 'datetime',
        'status' => SepaCollectionAttemptStatus::class,
        'sequence_type' => SepaSequenceType::class,
    ];

    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function sepaMandate(): BelongsTo { return $this->belongsTo(SepaMandate::class); }
    public function transaction(): BelongsTo { return $this->belongsTo(Transaction::class); }

    public function scopeUnresolved(Builder $query): Builder
    {
        return $query->where('status', SepaCollectionAttemptStatus::Submitted);
    }

    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where('fee_year', $year);
    }

    public function scopeInBatch(Builder $query, string $batchReference): Builder
    {
        return $query->where('batch_reference', $batchReference);
    }

    public function confirm(Transaction $transaction): void
    {
        $this->update([
            'status' => SepaCollectionAttemptStatus::Confirmed,
            'resolved_at' => now(),
            'transaction_id' => $transaction->id,
        ]);
    }

    public function markReturned(string $reason): void
    {
        $this->update([
            'status' => SepaCollectionAttemptStatus::Returned,
            'resolved_at' => now(),
            'return_reason' => $reason,
        ]);
    }
}
```

---

## 5. `SepaDirectDebitService` – wird auf reine XML-Erzeugung reduziert

Keine DB-Schreibzugriffe mehr, kein `markAsUsed()`, kein hartcodiertes
`PAIN_FORMAT`, keine hartcodierte `+5 weekdays`. Alles wird vom Aufrufer
übergeben – das macht die Settings-Verdrahtungs-Bugs aus Phase 1 strukturell
unmöglich, weil es keine internen Defaults mehr gibt, die man vergessen
könnte.

```php
final class SepaDirectDebitService
{
    /**
     * @param array<int, array{
     *     member: Member,
     *     mandate: SepaMandate,
     *     amount: int,
     *     remittanceInformation: string,
     *     endToEndId: string,
     *     sequenceType: SepaSequenceType,
     * }> $debits
     */
    public function generateBatch(
        array $debits,
        Account $creditorAccount,
        string $creditorId,
        Carbon $dueDate,
        string $painFormat,
    ): string;
}
```

Beim `addPaymentInfo`-Aufruf innerhalb dieser Methode: zweiter Parameter von
`TransferFileFacadeFactory::createDirectDebit()` ist `$initiatingPartyName` –
hier muss `$creditorAccount->name` stehen, **nicht**
`$creditorAccount->institute ?? $creditorAccount->name` wie aktuell im Code.
Das war der Bug, der den Banknamen statt des Vereinsnamens in den Header
geschrieben hat.

`generateSingle()` und `generateAndStore()` entfallen – ein einzelnes
Mitglied ist einfach ein Aufruf von `generateBatch()` mit einem Element,
Speichern auf Disk macht der Aufrufer.

---

## 6. `SepaCollectionService` – neu, orchestriert den ganzen Ablauf

```php
final class SepaCollectionService
{
    public function __construct(
        private readonly SepaDirectDebitService $sepaDirectDebit,
        private readonly SepaSettingsService $sepaSettings,
        private readonly SepaMandateService $mandateService,
        private readonly SepaXmlValidator $xmlValidator,
        private readonly EbicsService $ebicsService,
    ) {}

    /** Mitglieder mit aktivem Mandat, offenem Beitrag für $year, ohne bereits
     *  offenen SepaCollectionAttempt für dasselbe Jahr. */
    public function findOpenCandidates(int $year): Collection;

    /**
     * Erzeugt für jedes übergebene Mitglied einen SepaCollectionAttempt
     * (status=submitted, sequence_type je nach mandate->last_used_at
     * eingefroren) UND ruft markAsUsed() auf dem jeweiligen Mandat auf –
     * beides in derselben DB-Transaktion wie das XML-Erzeugen.
     *
     * @return array{xml: string, attempts: Collection, validation: SepaValidationResult}
     */
    public function createAttemptsAndGenerateXml(Collection $members, int $year): array;

    /** Erzeugt aus einem bestätigten Attempt die echte Transaction +
     *  MemberTransaction (status=booked direkt), setzt attempt->confirm(). */
    public function confirm(SepaCollectionAttempt $attempt): Transaction;

    /** Bestätigt alle offenen Attempts eines Batches auf einmal. */
    public function confirmBatch(string $batchReference): Collection;

    /** Reicht das XML per EBICS ein. Setzt NICHT automatisch auf confirmed –
     *  das bleibt immer ein separater, bewusster Schritt. */
    public function uploadToEbics(string $xmlContent): void;
}
```

Wichtig für `createAttemptsAndGenerateXml()`: die Validierung
(`SepaXmlValidator`) muss vor dem Zurückgeben geprüft werden, und jeder
Aufrufer (UI **und** CLI-Command) muss bei `!$validation->valid` abbrechen,
bevor irgendetwas an die Bank geht. Das war der Bug im
`SepaCollectFees`-Command, der das ignoriert hat.

---

## 7. `SepaReturnDebitService` – deutlich einfacher

```php
final class SepaReturnDebitService
{
    public function __construct(
        private readonly SepaCollectionService $collectionService,
    ) {}

    /**
     * Markiert den Attempt als returned, setzt sepa_mandate.last_used_at
     * zurück auf null (damit der nächste Versuch wieder als FRST gilt),
     * benachrichtigt das Mitglied.
     */
    public function handleReturn(
        SepaCollectionAttempt $attempt,
        string $returnReason,
        ?string $returnReference = null,
    ): void;

    /**
     * Erzeugt einen neuen Attempt für dasselbe fee_year. Prüfungen davor:
     * - Mandat ist B2b -> RuntimeException (kein Wiedereinzug erlaubt)
     * - es existiert bereits ein unresolved Attempt für member+fee_year -> RuntimeException
     * - mehr als 30 Tage seit $returnedAttempt->resolved_at -> RuntimeException
     *
     * @return array{xml: string, attempts: Collection, validation: SepaValidationResult}
     */
    public function recollect(SepaCollectionAttempt $returnedAttempt): array;
}
```

Es entsteht keine Transaction mehr für den fehlgeschlagenen Versuch, also
muss auch nichts mehr geklont werden. `fee_year` wird direkt vom
`returnedAttempt` übernommen statt wie bisher `now()->year` zu setzen – das
behebt den Jahres-Bug aus dem alten `recollect()`.

---

## 8. Anpassungen an bestehenden Komponenten

- **`Accounting/SepaCollection/Index/Page.php`**: `pendingCollections()`
  wird zu `findOpenCandidates()`. Neuer Tab-Bereich „Offene Einreichungen"
  zeigt unresolved `SepaCollectionAttempt`s, gruppiert nach
  `batch_reference`, mit Buttons „Bestätigen" (einzeln oder ganzer Batch) und
  „Rückläufer erfassen" (öffnet ein Formular für Grund + Referenz – das war
  der UI-Einstiegspunkt, der bisher komplett gefehlt hat).
- **`Member/Fees/Index.php`**: `generateSepaBatchXml()` ruft
  `SepaCollectionService` auf statt die Query zu duplizieren.
- **`Member/SepaMandate/Manage.php`**: `exportSingleSepaXml()` ruft
  `createAttemptsAndGenerateXml()` für genau ein Mitglied auf.

---

## 9. Reihenfolge der Umsetzung

1. Migration + Enums + Model `SepaCollectionAttempt` (inkl. Factory)
2. `SepaDirectDebitService` umbauen (inkl. institute/name-Fix)
3. `SepaCollectionService` neu schreiben
4. `SepaReturnDebitService` neu schreiben
5. UI-Komponenten umstellen
6. Phase-1-Fixes, die jetzt noch übrig sind (siehe Checkliste unten)
7. Tests neu schreiben – die bisherigen testen explizit das jetzt verworfene
   Verhalten (Transaction sofort bei Beitragserstellung) und sind keine
   gültige Spezifikation mehr

## 10. Phase-1-Checkliste (unabhängig vom Umbau, aber gleich mit erledigen)

- [ ] IBAN: Mod-97-Prüfziffer + länderspezifische Längenprüfung statt reiner Struktur-Regex
- [ ] BIC-Regex: Suffix exakt 2 oder 5 Zeichen, nicht `{2,5}`
- [ ] Gläubiger-ID-Regex: ebenfalls nur Struktur, keine Prüfziffer – optional nachrüsten
- [ ] `SepaMandateFactory::b2b()`: `SepaMandateType::B2B` → `SepaMandateType::B2b`
- [ ] `ebicsPassphrase()`: bei fehlgeschlagener Entschlüsselung Exception statt Ciphertext zurückgeben
- [ ] `EbicsService` Country-Code: aktuell hartcodiert `DE`, für AT/CH-Kunden später parametrisieren
- [ ] `mandate_date`-Eingabefeld im Mandats-Formular ergänzen (aktuell immer `now()`)
- [ ] Mandats-Ablauf nach 36 Monaten ohne Nutzung: kleiner Scheduled Command, der `SepaMandateStatus::Expired` setzt

## 11. Bewusst nicht jetzt gelöst

Ein Attempt, der erzeugt (XML generiert, Mandat als benutzt markiert), aber
nie wirklich eingereicht wurde, kann aktuell nicht „rückgängig" gemacht
werden. Ein `cancelled`-Status mit Reset von `last_used_at` wäre die
naheliegende Lösung, ist aber für den Start nicht nötig – im Zweifel
korrigiert der Kassenwart das Mandat manuell. Nachrüstbar, ohne dass
bestehende Daten migriert werden müssten.

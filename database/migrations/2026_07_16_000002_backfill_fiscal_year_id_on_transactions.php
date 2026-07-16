<?php

declare(strict_types=1);

use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    /**
     * Daten-Backfill: ordnet Bestandsbuchungen ihrem Geschäftsjahr zu.
     *
     * Beschlusslage (Agents/fiscal-year-concept.md):
     * - min/max-Range statt selectRaw('YEAR(date)') → treiber-agnostisch
     *   (SQLite + MySQL), nur Grammar-Helper. Model-Scopes bewusst NICHT
     *   referenziert, damit die Migration self-contained bleibt.
     * - Altjahres-FYs werden OFFEN angelegt (Beschluss 1). Kein fingierter
     *   Abschluss: closed_at ohne Snapshot/locked_at/DATEV wäre inkonsistent.
     * - booking_account_type_id wird deterministisch vom ältesten FY mit
     *   gesetztem Typ kopiert (Beschluss 3) – KEIN Dauer-Fallback in
     *   getOrCreate().
     * - Idempotent: whereNull('fiscal_year_id') macht Stop+Restart sicher.
     */
    public function up(): void
    {
        // Guard: dokumentierte Annahme (Schema: date ist NOT NULL)
        if (Transaction::whereNull('date')->exists()) {
            throw new RuntimeException(
                'Transactions mit date = NULL gefunden — Backfill-Annahme verletzt.'
            );
        }

        $minDate = Transaction::whereNull('fiscal_year_id')->min('date');

        if ($minDate === null) {
            return; // keine Altdaten ohne FY
        }

        $minYear = Carbon::parse($minDate)->year;
        $maxYear = Carbon::parse(
            Transaction::whereNull('fiscal_year_id')->max('date')
        )->year;

        // Kontenrahmen-Quelle: deterministisch, EINMAL vor der Schleife
        $sourceType = FiscalYear::query()
            ->whereNotNull('booking_account_type_id')
            ->orderBy('year')
            ->value('booking_account_type_id');

        foreach (range($minYear, $maxYear) as $year) {
            if (! Transaction::whereNull('fiscal_year_id')->whereYear('date', $year)->exists()) {
                continue; // keine leeren FYs für buchungsfreie Jahre
            }

            $fy = FiscalYear::getOrCreate($year);

            // getOrCreate() erbt den Kontenrahmen vom Vorjahr — bei
            // Altjahren ist das NULL. Deterministische Kopie hier in der
            // Migration (Beschluss 3).
            if ($fy->booking_account_type_id === null && $sourceType !== null) {
                $fy->update(['booking_account_type_id' => $sourceType]);
            }

            // QB-update() bewusst: Massen-Backfill soll KEINE Observer feuern.
            Transaction::query()
                ->whereNull('fiscal_year_id')
                ->whereYear('date', $year)
                ->update(['fiscal_year_id' => $fy->id]);
        }
    }

    public function down(): void
    {
        // Backfill ist nicht sinnvoll umkehrbar – die Spalte entfernt
        // die strukturelle Migration. Zuordnungen bewusst stehen lassen.
    }
};

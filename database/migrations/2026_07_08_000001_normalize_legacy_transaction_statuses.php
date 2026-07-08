<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * TransactionStatus wurde auf 'submitted' und 'booked' reduziert.
 *
 * Die Werte 'returned' und 'cancelled' wurden nie programmatisch gesetzt
 * (Rücklastschriften laufen über SepaCollectionAttemptStatus + Storno-Gegenbuchung,
 * Stornos über eine Gegenbuchung mit Audit-Eintrag in cancel_transactions).
 * Sie konnten aber theoretisch manuell über UI-Formulare gewählt werden.
 * Solche Alt-Zeilen werden defensiv auf 'booked' normalisiert, damit der
 * Enum-Cast beim Hydrieren nicht fehlschlägt.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('transactions')
            ->whereIn('status', ['returned', 'cancelled'])
            ->update(['status' => 'booked']);

        DB::table('cancel_transactions')
            ->whereIn('status', ['returned', 'cancelled'])
            ->update(['status' => 'booked']);
    }

    public function down(): void
    {
        // Nicht umkehrbar: Die Information, welche Zeilen zuvor
        // 'returned'/'cancelled' waren, geht bewusst verloren.
    }
};

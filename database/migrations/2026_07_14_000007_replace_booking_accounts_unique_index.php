<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ersetzt den Single-Index auf booking_accounts.number durch einen
     * Composite-Unique-Index auf (booking_account_type_id, number).
     *
     * Reihenfolge: drop alt → create neu (in einer Migration, da SQLite
     * keinen separaten ALTER TABLE DROP INDEX unterstützt).
     *
     * Der Backfill (Migration 5) muss vor dieser Migration gelaufen sein,
     * damit booking_account_type_id auf allen Zeilen gesetzt ist.
     * Sonst ignoriert SQLite NULL-Werte im Unique-Constraint.
     */
    public function up(): void
    {
        Schema::table('booking_accounts', function (Blueprint $table) {
            $table->dropUnique(['number']);
            $table->unique(['booking_account_type_id', 'number'], 'booking_accounts_type_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('booking_accounts', function (Blueprint $table) {
            $table->dropUnique('booking_accounts_type_number_unique');
            $table->unique(['number']);
        });
    }
};

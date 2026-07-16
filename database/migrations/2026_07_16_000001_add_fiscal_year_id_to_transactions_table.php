<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Strukturelle Migration: explizite FY-Zuordnung auf transactions.
     * Nullable, weil der Backfill (separate Migration mit späterem
     * Timestamp) die Zuordnung nachzieht; Nicht-Null-Garantie kommt
     * aus dem TransactionObserver (creating-Hook).
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->foreignId('fiscal_year_id')
                ->nullable()
                ->constrained('fiscal_years')
                ->nullOnDelete();
            $table->index('fiscal_year_id');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('fiscal_year_id');
        });
    }
};

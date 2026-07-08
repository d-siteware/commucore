<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Verknüpft den Storno-Audit-Eintrag mit der erzeugten Gegenbuchung.
 * Damit lässt sich zuverlässig erkennen, ob eine Transaktion bereits
 * storniert wurde bzw. ob sie selbst eine Storno-Gegenbuchung ist.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cancel_transactions', function (Blueprint $table) {
            $table->foreignId('reversal_transaction_id')
                ->nullable()
                ->constrained('transactions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cancel_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reversal_transaction_id');
        });
    }
};

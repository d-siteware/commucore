<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funding_transactions', function (Blueprint $table): void {
            // Ist-Zuordnung sitzt bewusst auf der Verknüpfung, nicht auf der
            // Transaction: eine Ausgabe kann über zwei funding_transactions-Zeilen
            // an zwei Förderungen mit je eigener Positions-Systematik hängen.
            $table->foreignId('funding_position_id')
                ->nullable()
                ->after('allocated_amount')
                ->constrained('funding_positions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('funding_transactions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('funding_position_id');
        });
    }
};

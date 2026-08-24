<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * member_transactions.member_id hatte keinerlei FK-Constraint —
     * verwaiste Datensätze (Import/Manualeingriff) crashten Views und
     * Exporte. Jetzt nullable + nullOnDelete: die Buchungs-Verknüpfung
     * überlebt, der Member-Verweis wird null.
     */
    public function up(): void
    {
        Schema::table('member_transactions', function (Blueprint $table): void {
            $table->unsignedBigInteger('member_id')->nullable()->change();
            $table->foreign('member_id')->references('id')->on('members')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('member_transactions', function (Blueprint $table): void {
            $table->dropForeign(['member_id']);
            $table->unsignedBigInteger('member_id')->nullable(false)->change();
        });
    }
};

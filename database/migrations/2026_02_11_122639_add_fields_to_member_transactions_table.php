<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('member_transactions', function (Blueprint $table) {
            $table->boolean('is_membership_fee')->default(false)->after('transaction_id');
            $table->year('fee_year')->nullable()->after('is_membership_fee');

            // Index für Performance bei Auswertungen
            $table->index(['member_id', 'is_membership_fee', 'fee_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_transactions', function (Blueprint $table) {
            //
        });
    }
};

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
        Schema::table('datev_exports', function (Blueprint $table) {
            $table->string('zip_path', 500)->nullable()->after('filename');
            $table->string('zip_hash', 64)->nullable()->after('zip_path');
            $table->string('sent_to_email', 255)->nullable()->after('zip_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('datev_exports', function (Blueprint $table) {
            $table->dropColumn(['zip_path', 'zip_hash', 'sent_to_email']);
        });
    }
};

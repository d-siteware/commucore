<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boxoffice_presets', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('booking_account_type_id')
                ->constrained('booking_account_types')
                ->cascadeOnDelete();
            $table->foreignId('booking_account_id')
                ->constrained('booking_accounts')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('priority')->default(0);

            $table->unique(['booking_account_type_id', 'booking_account_id'], 'boxoffice_presets_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boxoffice_presets');
    }
};

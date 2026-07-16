<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_account_mappings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('booking_account_type_id')
                ->constrained('booking_account_types')
                ->cascadeOnDelete();
            $table->string('account_type');
            $table->string('booking_account_number');

            $table->unique(['booking_account_type_id', 'account_type'], 'payment_mappings_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_account_mappings');
    }
};

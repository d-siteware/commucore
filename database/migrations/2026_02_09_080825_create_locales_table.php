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
        Schema::create('locales', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->boolean('active')->default(false);
            $table->string('decimal_separator', 1)->default('.');
            $table->string('thousands_separator', 1)->default(',');
            $table->string('date_format')->default('Y-m-d');
            $table->enum('name_order', ['first_last', 'last_first'])->default('first_last');
            $table->string('currency_symbol')->nullable();
            $table->enum('currency_position', ['before', 'after'])->default('after');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locales');
    }
};

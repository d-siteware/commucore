<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_account_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('datev_skr_code')->nullable();
            $table->unsignedTinyInteger('account_length')->default(5);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_account_types');
    }
};

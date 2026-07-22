<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_position_categories', function (Blueprint $table): void {
            $table->id();
            // System-Kategorien nutzen Klartext-Slugs, Tenant-eigene den
            // reservierten Präfix "custom:" (Kollisionssicherung beim Reseed).
            $table->string('slug')->unique();
            $table->string('name');
            $table->boolean('is_system')->default(false);
            $table->string('source')->default('custom'); // 'system' | 'custom'
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_position_categories');
    }
};

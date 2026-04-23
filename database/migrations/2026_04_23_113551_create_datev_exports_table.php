<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datev_exports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('account_report_id')->constrained()->onDelete('cascade');
            $table->foreignId('exported_by')->constrained('users');
            $table->string('filename');
            $table->timestamp('exported_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datev_exports');
    }
};

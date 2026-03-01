<?php

declare(strict_types=1);

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
        Schema::create('event_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('task');
            $table->string('status');
            $table->text('description')->nullable();
            $table->date('due_at')->nullable();
            $table->unsignedInteger('amount')->nullable();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }
};

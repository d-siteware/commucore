<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_transactions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('funding_id');
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedInteger('allocated_amount')->nullable()->comment('Teilbetrag in Cent – null = volle Transaktion');
            $table->foreign('funding_id')->references('id')->on('fundings')->cascadeOnDelete();
            $table->foreign('transaction_id')->references('id')->on('transactions')->cascadeOnDelete();
            $table->unique(['funding_id', 'transaction_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_transactions');
    }
};

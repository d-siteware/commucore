<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fundings', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('funder')->comment('Name des Fördergebers (z.B. Stadt München)');
            $table->text('description')->nullable();
            $table->string('status')->default('applied'); // FundingStatus enum
            $table->unsignedInteger('approved_amount')->nullable()->comment('Bewilligter Betrag in Cent');
            $table->date('funding_period_start')->nullable();
            $table->date('funding_period_end')->nullable();
            $table->string('reference')->nullable()->comment('Aktenzeichen / Fördernummer');
            $table->unsignedBigInteger('booking_account_id')->nullable();
            $table->foreign('booking_account_id')->references('id')->on('booking_accounts')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fundings');
    }
};

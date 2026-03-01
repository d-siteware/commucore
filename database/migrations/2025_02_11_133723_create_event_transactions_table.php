<?php

declare(strict_types=1);

use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
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
        Schema::create('event_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_name')->nullable()->index();
            $table->string('gender')->nullable();
            $table->foreignIdFor(Transaction::class);
            $table->foreignIdFor(Event::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_transactions');
    }
};

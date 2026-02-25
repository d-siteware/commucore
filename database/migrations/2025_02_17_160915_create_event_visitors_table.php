<?php

declare(strict_types=1);

use App\Enums\Gender;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Event\EventSubscription;
use App\Models\Membership\Member;
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
        Schema::create('event_visitors', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->index();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->foreignIdFor(Event::class);
            $table->enum('gender', Gender::toArray())->nullable();
            $table->foreignIdFor(Transaction::class)->nullable()->index();
            $table->foreignIdFor(Member::class)->nullable()->index();
            $table->foreignIdFor(EventSubscription::class)->nullable()->index();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_visitors');
    }
};

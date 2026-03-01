<?php

declare(strict_types=1);

use App\Models\Accounting\Account;
use App\Models\Accounting\BookingAccount;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('label');
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('amount_gross');
            $table->unsignedtinyInteger('vat');
            $table->unsignedInteger('tax')->nullable();
            $table->unsignedInteger('amount_net');
            $table->foreignIdFor(Account::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(BookingAccount::class)->nullable();
            $table->string('type');
            $table->string('status');
            $table->timestamps();
        });
    }
};

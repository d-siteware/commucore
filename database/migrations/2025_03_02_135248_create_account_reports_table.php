<?php

declare(strict_types=1);

use App\Enums\ReportStatus;
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
        Schema::create('account_reports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->integer('starting_amount');
            $table->integer('end_amount');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->integer('total_income')->default(0);
            $table->integer('total_expenditure')->default(0);
            $table->string('status')->default(ReportStatus::draft);
            $table->text('notes')->nullable();
        });
    }
};

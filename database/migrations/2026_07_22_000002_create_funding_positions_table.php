<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('funding_positions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('funding_id')->constrained('fundings')->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('budget')->default(0)->comment('Plan-Budget brutto in Cent');
            $table->foreignId('funding_position_category_id')->nullable()->constrained('funding_position_categories')->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete()->comment('Verantwortlicher');
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funding_positions');
    }
};

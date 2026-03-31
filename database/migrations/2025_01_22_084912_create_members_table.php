<?php

declare(strict_types=1);

use App\Models\User;
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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('applied_at');
            $table->date('verified_at')->nullable();
            $table->date('entered_at')->nullable();
            $table->date('left_at')->nullable();
            $table->boolean('is_deducted')->default(false);
            $table->text('deduction_reason')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('address')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('locale')->nullable()->default('de');
            $table->string('gender')->nullable();
            $table->string('type')->nullable();
            $table->foreignIdFor(User::class)->nullable()->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};

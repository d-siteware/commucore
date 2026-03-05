<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_applications', function (Blueprint $table): void {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('email');
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('locale', 10)->default('de');
            $table->text('address')->nullable();
            $table->string('zip')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Deutschland');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('family_status')->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_deducted')->default(false);
            $table->string('deduction_reason')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('gdpr_consent_at')->nullable();
            $table->timestamp('newsletter_consent_at')->nullable();
            $table->timestamp('photo_consent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_applications');
    }
};

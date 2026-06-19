<?php

declare(strict_types=1);

use App\Models\Membership\Member;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sepa_mandates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Member::class)->constrained()->cascadeOnDelete();
            $table->string('mandate_reference', 35)->unique();
            $table->string('iban', 34);
            $table->string('bic', 11)->nullable();
            $table->string('account_holder');
            $table->date('mandate_date');
            $table->string('mandate_type', 10)->default('core');
            $table->string('status', 20)->default('pending');
            $table->foreignId('signed_document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('payment_completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'status']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->string('iban', 34)->nullable()->after('fee_type');
            $table->string('bic', 11)->nullable()->after('iban');
            $table->string('account_holder')->nullable()->after('bic');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['iban', 'bic', 'account_holder']);
        });

        Schema::dropIfExists('sepa_mandates');
    }
};

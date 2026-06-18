<?php

declare(strict_types=1);

use App\Enums\SepaCollectionAttemptStatus;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sepa_collection_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Member::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(SepaMandate::class)->nullable()->constrained()->nullOnDelete();
            $table->integer('amount');
            $table->integer('fee_year');
            $table->string('remittance_information');
            $table->string('end_to_end_id');
            $table->date('due_date');
            $table->string('sequence_type', 10);
            $table->string('batch_reference')->nullable();
            $table->string('status', 20)->default(SepaCollectionAttemptStatus::Submitted->value);
            $table->timestamp('resolved_at')->nullable();
            $table->string('return_reason')->nullable();
            $table->foreignIdFor(Transaction::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reversal_transaction_id')->nullable()->after('transaction_id')->constrained('transactions')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['member_id', 'fee_year']);
            $table->index('batch_reference');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sepa_collection_attempts');
    }
};

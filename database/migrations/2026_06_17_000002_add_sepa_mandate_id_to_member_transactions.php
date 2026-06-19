<?php

declare(strict_types=1);

use App\Models\Membership\SepaMandate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_transactions', function (Blueprint $table) {
            $table->foreignIdFor(SepaMandate::class)->nullable()->after('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('member_transactions', function (Blueprint $table) {
            $table->dropColumn('sepa_mandate_id');
        });
    }
};

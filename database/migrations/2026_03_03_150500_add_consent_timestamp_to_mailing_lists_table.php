<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds auditable timestamps to mailing_lists.
 *
 * - terms_accepted_at: replaces bare boolean (keep boolean for BC).
 * - unsubscribed_at:   soft opt-out instead of hard delete.
 *   Rows with unsubscribed_at MUST never receive mail.
 *   Hard deletion runs via PurgeUnsubscribedMailingListCommand
 *   after configurable retention (default: 30 days post-unsubscribe).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mailing_lists', function (Blueprint $table): void {
            $table->timestamp('terms_accepted_at')
                ->nullable()
                ->after('terms_accepted');

            $table->timestamp('unsubscribed_at')
                ->nullable()
                ->after('terms_accepted_at');
        });

        // Back-fill: use created_at as conservative approximation.
        DB::table('mailing_lists')
            ->where('terms_accepted', true)
            ->whereNull('terms_accepted_at')
            ->update(['terms_accepted_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('mailing_lists', function (Blueprint $table): void {
            $table->dropColumn(['terms_accepted_at', 'unsubscribed_at']);
        });
    }
};

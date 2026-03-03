<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces bare boolean consentNotification with an auditable timestamp.
 * Adds data_purge_after for scheduled hard-deletion (e.g. event_date + 30 days).
 *
 * The old boolean column is kept for BC – drop it in a follow-up migration
 * once application code no longer references it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_subscriptions', function (Blueprint $table): void {
            $table->timestamp('notification_consent_at')
                ->nullable()
                ->after('consentNotification');

            $table->date('data_purge_after')
                ->nullable()
                ->after('notification_consent_at');
        });

        // Back-fill: use updated_at as conservative approximation for existing rows.
        DB::table('event_subscriptions')
            ->where('consentNotification', true)
            ->whereNull('notification_consent_at')
            ->update(['notification_consent_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('event_subscriptions', function (Blueprint $table): void {
            $table->dropColumn(['notification_consent_at', 'data_purge_after']);
        });
    }
};

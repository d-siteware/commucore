<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Previous migrations approximated consent times using created_at/updated_at.
        DB::table('event_subscriptions')
            ->whereNotNull('notification_consent_at')
            ->update(['notification_consent_at' => null]);

        DB::table('mailing_lists')
            ->whereNotNull('terms_accepted_at')
            ->update(['terms_accepted_at' => null]);

        DB::table('event_subscriptions')
            ->select(['id', 'event_id'])
            ->whereNull('data_purge_after')
            ->orderBy('id')
            ->chunkById(200, static function ($subscriptions): void {
                foreach ($subscriptions as $subscription) {
                    $eventDate = DB::table('events')
                        ->where('id', $subscription->event_id)
                        ->value('event_date');

                    $purgeAfter = $eventDate
                        ? Carbon::parse($eventDate)->addDays(30)->toDateString()
                        : today()->toDateString();

                    DB::table('event_subscriptions')
                        ->where('id', $subscription->id)
                        ->update(['data_purge_after' => $purgeAfter]);
                }
            });
    }

    public function down(): void
    {
        // Consent timestamps and retention dates cannot be safely reconstructed.
    }
};

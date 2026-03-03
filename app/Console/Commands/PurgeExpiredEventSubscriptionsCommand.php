<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Hard-deletes EventSubscription rows whose data_purge_after date has passed.
 *
 * DSGVO Art. 17 – Recht auf Löschung.
 * Gäste haben keinen Account; ihre Daten dürfen nach Ablauf der Frist
 * nicht mehr gespeichert werden.
 *
 * Default: löscht Rows mit data_purge_after <= heute.
 *
 * Usage:
 *   php artisan gdpr:purge-event-subscriptions [--dry-run]
 *
 * Scheduling (routes/console.php):
 *   Schedule::command('gdpr:purge-event-subscriptions')->daily();
 */
final class PurgeExpiredEventSubscriptionsCommand extends Command
{
    /** @var string */
    protected $signature = 'gdpr:purge-event-subscriptions
                            {--dry-run : List affected rows without deleting}';

    /** @var string */
    protected $description = 'Hard-delete expired event subscription data (DSGVO Art. 17)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $today = Carbon::today()->toDateString();

        $query = DB::table('event_subscriptions')
            ->whereNotNull('data_purge_after')
            ->where('data_purge_after', '<=', $today);

        $count = $query->count();

        if ($count === 0) {
            $this->info('No expired event subscriptions to purge.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%s %d event subscription(s) with data_purge_after <= %s…',
            $dryRun ? '[DRY RUN] Would delete' : 'Deleting',
            $count,
            $today,
        ));

        if ($dryRun) {
            $rows = $query->select(['id', 'email', 'event_id', 'data_purge_after'])->get();

            $this->table(
                ['ID', 'email', 'event_id', 'data_purge_after'],
                $rows->map(static fn (object $row): array => [
                    $row->id,
                    $row->email,
                    $row->event_id,
                    $row->data_purge_after,
                ])->toArray(),
            );

            return self::SUCCESS;
        }

        // Chunk-delete to avoid locking large tables.
        $deleted = 0;

        $query->select('id')->chunkById(200, function (\Illuminate\Support\Collection $chunk) use (&$deleted): void {
            $ids = $chunk->pluck('id')->all();

            DB::table('event_subscriptions')->whereIn('id', $ids)->delete();

            $deleted += count($ids);

            Log::info('gdpr.purged_event_subscriptions', [
                'count' => count($ids),
                'command' => self::class,
            ]);
        });

        $this->info("Done. Deleted: {$deleted} row(s).");

        return self::SUCCESS;
    }
}

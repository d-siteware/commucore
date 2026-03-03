<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Hard-deletes mailing_list rows that have been unsubscribed for longer
 * than the configured retention period (default: 30 days).
 *
 * DSGVO Art. 17 – Recht auf Löschung.
 * The 30-day window gives the subscriber time to re-subscribe if they
 * unsubscribed accidentally, while ensuring timely deletion.
 *
 * Usage:
 *   php artisan gdpr:purge-unsubscribed-mailing-list [--dry-run] [--days=30]
 *
 * Scheduling (routes/console.php):
 *   Schedule::command('gdpr:purge-unsubscribed-mailing-list')->daily();
 */
final class PurgeUnsubscribedMailingListCommand extends Command
{
    /** @var string */
    protected $signature = 'gdpr:purge-unsubscribed-mailing-list
                            {--dry-run : List affected rows without deleting}
                            {--days=30 : Retention period in days after unsubscribed_at}';

    /** @var string */
    protected $description = 'Hard-delete mailing list entries unsubscribed longer than retention period (DSGVO Art. 17)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $retentionDays = (int) $this->option('days');

        if ($retentionDays < 1) {
            $this->error('--days must be at least 1.');

            return self::FAILURE;
        }

        $cutoff = Carbon::now()->subDays($retentionDays);

        $this->info(sprintf(
            '%s entries unsubscribed before %s (retention: %d days)…',
            $dryRun ? '[DRY RUN] Would delete' : 'Deleting',
            $cutoff->toDateTimeString(),
            $retentionDays,
        ));

        $query = DB::table('mailing_lists')
            ->whereNotNull('unsubscribed_at')
            ->where('unsubscribed_at', '<', $cutoff->toDateTimeString());

        $count = $query->count();

        if ($count === 0) {
            $this->info('No unsubscribed entries to purge.');

            return self::SUCCESS;
        }

        $this->info("Found {$count} entry/entries to purge.");

        if ($dryRun) {
            $rows = $query->select(['id', 'email', 'unsubscribed_at'])->get();

            $this->table(
                ['ID', 'email', 'unsubscribed_at'],
                $rows->map(static fn (object $row): array => [
                    $row->id,
                    $row->email,
                    $row->unsubscribed_at,
                ])->toArray(),
            );

            return self::SUCCESS;
        }

        $deleted = 0;

        $query->select('id')->chunkById(200, function (\Illuminate\Support\Collection $chunk) use (&$deleted): void {
            $ids = $chunk->pluck('id')->all();

            DB::table('mailing_lists')->whereIn('id', $ids)->delete();

            $deleted += count($ids);

            Log::info('gdpr.purged_mailing_list_entries', [
                'count' => count($ids),
                'command' => self::class,
            ]);
        });

        $this->info("Done. Deleted: {$deleted} entry/entries.");

        return self::SUCCESS;
    }
}

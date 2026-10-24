<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Event\EventSubscription;
use App\Models\Event\EventVisitor;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Deletes expired signup data and anonymizes visitor PII after its retention period.
 *
 * DSGVO Art. 17 – Recht auf Löschung.
 * Event and transaction links are retained for accounting and aggregate counts.
 *
 * Signup rows expire on data_purge_after; visitor records expire 30 days
 * after the event date. Orphaned personal data is anonymized immediately.
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
                            {--dry-run : Show affected row counts without changing data}';

    /** @var string */
    protected $description = 'Purge expired event signups and anonymize visitor data';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $today = Carbon::today()->toDateString();
        $oldestRetainedEventDate = Carbon::today()->subDays(30)->toDateString();

        $subscriptions = EventSubscription::query()
            ->where(function (Builder $query) use ($today, $oldestRetainedEventDate): void {
                $query->where(function (Builder $query) use ($today): void {
                    $query->whereNotNull('data_purge_after')
                        ->whereDate('data_purge_after', '<=', $today);
                })->orWhere(function (Builder $query) use ($oldestRetainedEventDate): void {
                    $query->whereNull('data_purge_after')
                        ->where(function (Builder $query) use ($oldestRetainedEventDate): void {
                            $query->whereHas('event', fn (Builder $event) => $event->whereDate('event_date', '<=', $oldestRetainedEventDate))
                                ->orWhereDoesntHave('event');
                        });
                });
            });

        $visitors = EventVisitor::query()
            ->where(function (Builder $query) use ($today, $oldestRetainedEventDate): void {
                $query->whereHas('event', fn (Builder $event) => $event->whereDate('event_date', '<=', $oldestRetainedEventDate))
                    ->orWhereHas('eventSubscription', fn (Builder $subscription) => $subscription
                        ->whereNotNull('data_purge_after')
                        ->whereDate('data_purge_after', '<=', $today))
                    ->orWhere(function (Builder $query): void {
                        $query->whereDoesntHave('event')->whereDoesntHave('eventSubscription');
                    });
            })
            ->where('name', '!=', 'Anonymisiert')
            ->where(function (Builder $query): void {
                $query->where('name', '!=', 'Karte Abendkasse')
                    ->orWhereNotNull('email')
                    ->orWhereNotNull('phone')
                    ->orWhereNotNull('gender')
                    ->orWhereNotNull('member_id');
            });

        $subscriptionCount = $subscriptions->count();
        $visitorCount = $visitors->count();

        if ($subscriptionCount === 0 && $visitorCount === 0) {
            $this->info('No expired event data to purge.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%s %d event subscription(s) and anonymize %d visitor record(s).',
            $dryRun ? '[DRY RUN] Would delete' : 'Deleting',
            $subscriptionCount,
            $visitorCount,
        ));

        if ($dryRun) {
            return self::SUCCESS;
        }

        $visitors->select('id')->chunkById(200, function (Collection $chunk): void {
            $ids = $chunk->pluck('id')->all();

            DB::table('event_visitors')->whereIn('id', $ids)->update([
                'name' => 'Anonymisiert',
                'email' => null,
                'phone' => null,
                'gender' => null,
                'member_id' => null,
                'event_subscription_id' => null,
                'updated_at' => now(),
            ]);

            Log::info('gdpr.anonymized_event_visitors', [
                'count' => count($ids),
                'command' => self::class,
            ]);
        });

        $subscriptions->select('id')->chunkById(200, function (Collection $chunk): void {
            $ids = $chunk->pluck('id')->all();

            DB::table('event_subscriptions')->whereIn('id', $ids)->delete();

            Log::info('gdpr.purged_event_subscriptions', [
                'count' => count($ids),
                'command' => self::class,
            ]);
        });

        $this->info(sprintf(
            'Done. Deleted: %d subscription(s); anonymized: %d visitor(s).',
            $subscriptionCount,
            $visitorCount,
        ));

        return self::SUCCESS;
    }
}

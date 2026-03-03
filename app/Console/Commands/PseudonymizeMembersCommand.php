<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Pseudonymises member personal data after the statutory retention period.
 *
 * Retention (§ 147 AO / § 257 HGB):
 *  - Personal data without financial relevance: 3 years after left_at
 *  - Financial-linked data:                    10 years after left_at
 *
 * Financial rows (member_transactions) are NOT touched – they remain
 * referenced via FK from the now-pseudonymised member row.
 *
 * Usage:
 *   php artisan gdpr:pseudonymize-members [--dry-run] [--years=3]
 *
 * Scheduling (routes/console.php):
 *   Schedule::command('gdpr:pseudonymize-members')->monthly();
 */
final class PseudonymizeMembersCommand extends Command
{
    /** @var string */
    protected $signature = 'gdpr:pseudonymize-members
                            {--dry-run : List affected members without making changes}
                            {--years=3 : Retention period in years after left_at}';

    /** @var string */
    protected $description = 'Pseudonymise member personal data after retention period (DSGVO Art. 17)';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $retentionYears = (int) $this->option('years');

        if ($retentionYears < 1) {
            $this->error('--years must be at least 1.');

            return self::FAILURE;
        }

        $cutoff = Carbon::now()->subYears($retentionYears);

        $this->info(sprintf(
            '%s members who left before %s (retention: %d years)…',
            $dryRun ? '[DRY RUN] Would pseudonymise' : 'Pseudonymising',
            $cutoff->toDateString(),
            $retentionYears,
        ));

        $candidates = DB::table('members')
            ->whereNotNull('left_at')
            ->where('left_at', '<', $cutoff->toDateString())
            ->whereNull('pseudonymized_at')
            ->select(['id', 'name', 'first_name', 'email', 'left_at'])
            ->get();

        if ($candidates->isEmpty()) {
            $this->info('No members require pseudonymisation.');

            return self::SUCCESS;
        }

        $this->info("Found {$candidates->count()} member(s) to process.");

        if ($dryRun) {
            $this->table(
                ['ID', 'name', 'first_name', 'email', 'left_at'],
                $candidates->map(static fn (object $row): array => [
                    $row->id,
                    $row->name,
                    $row->first_name ?? '–',
                    $row->email ?? '–',
                    $row->left_at,
                ])->toArray(),
            );

            return self::SUCCESS;
        }

        $processedCount = 0;
        $errorCount = 0;

        foreach ($candidates as $member) {
            try {
                DB::table('members')
                    ->where('id', $member->id)
                    ->update($this->pseudonymisedFields((int) $member->id));

                Log::info('gdpr.pseudonymized_member', [
                    'member_id' => $member->id,
                    'left_at' => $member->left_at,
                    'command' => self::class,
                ]);

                $processedCount++;
            } catch (\Throwable $e) {
                Log::error('gdpr.pseudonymize_member_failed', [
                    'member_id' => $member->id,
                    'error' => $e->getMessage(),
                ]);

                $this->warn("Failed for member #{$member->id}: {$e->getMessage()}");
                $errorCount++;
            }
        }

        $this->info("Done. Pseudonymised: {$processedCount}, errors: {$errorCount}.");

        return $errorCount === 0 ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Returns pseudonymised column values for a member row.
     * Member ID is embedded in placeholders so rows stay distinguishable
     * in DB dumps without exposing any real personal data.
     *
     * @return array<string, string|null>
     */
    public function pseudonymisedFields(int $memberId): array
    {
        $token = "PSEUDONYMIZED_{$memberId}";

        return [
            'name' => $token,
            'first_name' => null,
            'email' => null,
            'phone' => null,
            'mobile' => null,
            'address' => null,
            'zip' => null,
            'city' => null,
            'country' => null,
            'gender' => null,
            'birth_date' => null,
            'birth_place' => null,
            'citizenship' => null,
            'family_status' => null,
            'gdpr_consent_at' => null,
            'newsletter_consent_at' => null,
            'newsletter_consent_revoked_at' => null,
            'photo_consent_at' => null,
            'photo_consent_revoked_at' => null,
            'pseudonymized_at' => Carbon::now()->toDateTimeString(),
        ];
    }
}

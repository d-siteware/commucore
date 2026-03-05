<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Membership\MemberApplication;
use Illuminate\Console\Command;

final class PruneExpiredApplications extends Command
{
    protected $signature = 'members:prune-applications';

    protected $description = 'Löscht abgelaufene, unverifizierte Bewerbungen';

    public function handle(): int
    {
        $deleted = MemberApplication::query()
            ->whereNull('verified_at')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Gelöscht: {$deleted} abgelaufene Bewerbungen.");

        return self::SUCCESS;
    }
}

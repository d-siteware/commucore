<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Import\MemberImportBackup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class PruneImportBackupsCommand extends Command
{
    protected $signature = 'commucore:prune-import-backups';

    protected $description = 'Löscht Import-Backups älter als 24h (Mitglieder-PII, Retention).';

    public function handle(): int
    {
        $deleted = MemberImportBackup::pruneExpired();

        if ($deleted > 0) {
            Log::info('import-backup.prune', ['deleted' => $deleted]);
        }

        $this->components->info("{$deleted} alte Import-Backups gelöscht.");

        return self::SUCCESS;
    }
}

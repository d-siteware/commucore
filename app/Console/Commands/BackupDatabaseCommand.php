<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

final class BackupDatabaseCommand extends Command
{
    protected $signature = 'commucore:backup-database {--keep=5 : Anzahl der zu behaltenden Backups}';

    protected $description = 'Erstellt eine konsistente Kopie der SQLite-Datenbank dieser Instanz.';

    public function handle(): int
    {
        $databasePath = config('database.connections.sqlite.database');
        $backupDir = dirname($databasePath).'/backups';

        File::ensureDirectoryExists($backupDir);

        $backupPath = $backupDir.'/backup-'.now()->format('Y-m-d_His').'.sqlite';
        $quotedPath = DB::connection()->getPdo()->quote($backupPath);

        DB::statement("VACUUM INTO {$quotedPath}");

        $this->components->info("Backup erstellt: {$backupPath}");

        $this->pruneOldBackups($backupDir, (int) $this->option('keep'));

        return self::SUCCESS;
    }

    private function pruneOldBackups(string $backupDir, int $keep): void
    {
        collect(File::files($backupDir))
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values()
            ->slice($keep)
            ->each(fn ($file) => File::delete($file->getPathname()));
    }
}
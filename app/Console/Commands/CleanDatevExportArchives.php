<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Accounting\Datev\DatevExportMailService;
use Illuminate\Console\Command;

final class CleanDatevExportArchives extends Command
{
    protected $signature = 'datev:clean-archives
        {--days=30 : Löscht ZIP-Archive älter als N Tage}';

    protected $description = 'Entfernt alte DATEV-Export-ZIP-Archive von der Festplatte';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $deleted = DatevExportMailService::cleanupOldZips($days);

        $this->info("{$deleted} alte DATEV-ZIP-Archive bereinigt.");

        return self::SUCCESS;
    }
}

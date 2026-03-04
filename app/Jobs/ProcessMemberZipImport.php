<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\MemberImportCompleted;
use App\Models\User;
use App\Services\Import\MemberFieldMapper;
use App\Services\Import\MemberImportBackup;
use App\Services\Import\MemberImporter;
use App\Services\Import\ZipImportHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

final class ProcessMemberZipImport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** Maximale Versuche bei Fehler */
    public int $tries = 1;

    /** Timeout in Sekunden – 10 Minuten für große ZIPs */
    public int $timeout = 600;

    public function __construct(
        public readonly string $storedZipPath,
        public readonly int $userId,
    ) {}

    public function handle(): void
    {
        $user = User::findOrFail($this->userId);

        Log::info('member.zip_import.started', [
            'user_id' => $this->userId,
            'zip_path' => $this->storedZipPath,
        ]);

        try {
            // ZIP extrahieren
            $absolutePath = Storage::disk('local')->path($this->storedZipPath);
            $tmpFile = new UploadedFile($absolutePath, 'import.zip', 'application/zip', null, true);
            $extracted = ZipImportHandler::extract($tmpFile);

            // CSV parsen
            $csvFile = new UploadedFile(
                $extracted['csv_path'],
                'members_all.csv',
                'text/csv',
                null,
                true,
            );

            $parsed = \App\Services\Import\MemberCsvParser::parse($csvFile);

            // Felder auto-mappen
            $analysis = MemberFieldMapper::analyse($parsed['headers']);
            $fieldMap = $analysis['auto_mapped'];
            $mappedRows = array_map(
                static fn (array $row): array => MemberFieldMapper::applyMapping($row, $fieldMap),
                $parsed['all_rows'],
            );

            // Backup erstellen
            $backupPath = MemberImportBackup::create($this->userId);

            // Import
            $protocol = MemberImporter::import($mappedRows, $this->userId);

            // Dokumente kopieren
            if ($extracted['document_map'] !== []) {
                ZipImportHandler::copyDocuments($extracted['document_map']);
            }

            // Cleanup
            ZipImportHandler::cleanup($extracted['extract_dir']);
            Storage::disk('local')->delete($this->storedZipPath);

            // E-Mail
            Mail::to($user->email)->send(new MemberImportCompleted(
                user: $user,
                protocol: $protocol,
                backupDownloadUrl: MemberImportBackup::downloadUrl($backupPath),
                importedAt: now()->toDateTimeString(),
            ));

            Log::info('member.zip_import.completed', [
                'user_id' => $this->userId,
                'imported' => $protocol['imported'],
                'skipped' => $protocol['skipped'],
            ]);

        } catch (\Throwable $e) {
            Log::error('member.zip_import.failed', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);

            // Benutzer über Fehler informieren
            Mail::to($user->email)->send(new \App\Mail\MemberImportFailed(
                user: $user,
                reason: $e->getMessage(),
            ));

            // ZIP aufräumen
            Storage::disk('local')->delete($this->storedZipPath);

            throw $e;
        }
    }
}

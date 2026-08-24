<?php

declare(strict_types=1);

namespace App\Livewire\Member\Import\Steps;

use App\Livewire\Traits\HandlesErrors;
use App\Mail\MemberImportCompleted;
use App\Services\Import\MemberImportBackup;
use App\Services\Import\MemberImporter;
use App\Services\Import\ZipImportHandler;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

final class ImportStep extends Component
{
    use HandlesErrors;
    /** @var array<int, array<string, string>> */
    public array $mappedRows = [];

    public ?string $backupPath = null;

    public string $importType = '';

    public bool $importStarted = false;

    public bool $importFinished = false;

    public bool $isRollingBack = false;

    /** @var array{imported: int, skipped: int, errors: array<int, array{row: int, reason: string}>, duration_ms: int}|null */
    public ?array $protocol = null;

    public string $importCacheKey = '';

    public function mount(): void
    {
        if ($this->mappedRows === []) {
            $this->mappedRows = \Cache::get($this->importCacheKey.'_mapped', []);
        }
    }

    public function startImport(): void
    {
        try {
            $this->importStarted = true;

            /** @var \App\Models\User $user */
            $user = auth()->user();

            $protocol = MemberImporter::import($this->mappedRows, $user->id);

            // ZIP-Dokumente kopieren falls vorhanden
            $documentMap = session('import_document_map', []);
            if ($documentMap !== []) {
                ZipImportHandler::copyDocuments($documentMap);

                $extractDir = session('import_extract_dir');
                if ($extractDir !== null) {
                    ZipImportHandler::cleanup($extractDir);
                }

                session()->forget(['import_document_map', 'import_extract_dir']);
            }

            $this->protocol = $protocol;
            $this->importFinished = true;

            // cleanup cache
            \Cache::forget($this->importCacheKey);
            \Cache::forget($this->importCacheKey.'_mapped');

            // E-Mail versenden
            Mail::to($user->email)->send(new MemberImportCompleted(
                user: $user,
                protocol: $protocol,
                backupDownloadUrl: $this->backupPath
                    ? MemberImportBackup::downloadUrl($this->backupPath)
                    : '',
                importedAt: now()->toDateTimeString(),
            ));
        } catch (\Throwable $e) {
            $this->importStarted = false;
            $this->handleError('Import starten fehlgeschlagen', $e);
        }
    }

    public function rollback(): void
    {
        try {
            if ($this->backupPath === null) {
                return;
            }

            if (! MemberImportBackup::isRollbackAllowed($this->backupPath)) {
                return;
            }

            $this->isRollingBack = true;

            MemberImportBackup::restore($this->backupPath);
            MemberImportBackup::delete($this->backupPath);

            $this->isRollingBack = false;
            $this->importFinished = false;
            $this->importStarted = false;
            $this->protocol = null;

            $this->dispatch('import-complete');
        } catch (\Throwable $e) {
            $this->isRollingBack = false;
            $this->handleError('Import-Rollback fehlgeschlagen', $e);
        }
    }

    public function canRollback(): bool
    {
        return $this->backupPath !== null
            && MemberImportBackup::isRollbackAllowed($this->backupPath);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.import.steps.import-step');
    }
}

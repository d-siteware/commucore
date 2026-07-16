<?php

declare(strict_types=1);

namespace App\Livewire\Member\Import\Steps;

use App\Livewire\Traits\HandlesErrors;
use App\Models\Membership\Member;
use App\Services\Import\MemberImportBackup;
use Livewire\Component;

final class PreviewStep extends Component
{
    use HandlesErrors;
    /** @var array<int, array<string, string>> */
    public array $mappedRows = [];

    public int $totalRows = 0;

    public ?string $backupPath = null;

    public bool $backupCreated = false;

    public bool $isCreatingBackup = false;

    /** @var array<int, array<string, string>> */
    public array $duplicates = [];

    public string $importCacheKey = '';

    public function mount(): void
    {
        $cached = \Cache::get($this->importCacheKey.'_mapped', []);

        $this->mappedRows = $cached;
        $this->totalRows = count($cached);

        $this->detectDuplicates();
    }

    public function createBackup(): void
    {
        try {
            $this->isCreatingBackup = true;

            $userId = auth()->id() ?? 0;

            $this->backupPath = MemberImportBackup::create($userId);
            $this->backupCreated = true;
            $this->isCreatingBackup = false;

            $this->dispatch('backup-complete', backupPath: $this->backupPath);
        } catch (\Throwable $e) {
            $this->isCreatingBackup = false;
            $this->handleError('Backup erstellen fehlgeschlagen', $e);
        }
    }

    public function backupDownloadUrl(): ?string
    {
        if ($this->backupPath === null) {
            return null;
        }

        return MemberImportBackup::downloadUrl($this->backupPath);
    }

    private function detectDuplicates(): void
    {
        $emails = array_filter(array_column($this->mappedRows, 'email'));

        if ($emails === []) {
            return;
        }

        $existingEmails = Member::query()
            ->whereIn('email', $emails)
            ->pluck('email')
            ->map(static fn (string $e): string => strtolower($e))
            ->toArray();

        $this->duplicates = array_filter(
            $this->mappedRows,
            static fn (array $row): bool => in_array(
                strtolower($row['email'] ?? ''),
                $existingEmails,
                strict: true,
            ),
        );
    }

    /** Erste 10 Zeilen für Vorschau */
    public function previewRows(): array
    {
        return array_slice($this->mappedRows, 0, 10);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.import.steps.preview-step');
    }
}

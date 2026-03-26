<?php

declare(strict_types=1);

namespace App\Livewire\Member\Import;

use App\Enums\MemberExportType;
use Livewire\Component;

/**
 * Import Wizard – orchestriert die 4 Import-Steps.
 *
 * Steps:
 *   1 = Upload      → Datei hochladen, Typ wählen
 *   2 = Mapping     → Felder zuordnen, Enum-Mapping
 *   3 = Preview     → Vorschau, Backup erstellen
 *   4 = Import      → Import ausführen, Protokoll
 */
final class Page extends Component
{
    public int $currentStep = 1;

    public string $importType = MemberExportType::STAMMDATEN->value;

    /** Backup-Pfad – wird in Step 3 gesetzt */
    public ?string $backupPath = null;

    /** Gemappte Zeilen – werden zwischen Steps weitergegeben */
    /** @var array<int, array<string, string>> */
    public array $mappedRows = [];

    /** Feld-Mapping: csvHeader → commuCoreField */
    /** @var array<string, string> */
    public array $fieldMap = [];

    /** Enum-Mapping: field → [unknownValue => commuCoreValue] */
    /** @var array<string, array<string, string>> */
    public array $enumMap = [];

    /** CSV-Header aus der hochgeladenen Datei */
    /** @var string[] */
    public array $csvHeaders = [];

    /** Gesamtanzahl der Zeilen in der Datei */
    public int $totalRows = 0;

    public string $importCacheKey = '';

    // ── Step-Navigation ───────────────────────────────────────────────────────

    public function nextStep(): void
    {
        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep(int $step): void
    {
        if ($step >= 1 && $step <= 4 && $step <= $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    // ── Event Listener – Steps kommunizieren nach oben ────────────────────────

    /**
     * UploadStep meldet: Datei wurde erfolgreich geparst.
     *
     * @param array{
     *     headers: string[],
     *     all_rows: array<int, array<string, string>>,
     *     total_rows: int,
     *     import_type: string,
     *     cache_key: string,
     * } $data
     */
    public function handleUploadComplete(array $data): void
    {
        $this->csvHeaders = $data['headers'];
        $this->totalRows = $data['total_rows'];
        $this->importType = $data['import_type'];
        $this->importCacheKey = $data['cache_key'];

        $this->nextStep();
    }

    /**
     * MappingStep meldet: Mapping abgeschlossen.
     *
     * @param array{
     *     field_map: array<string, string>,
     *     enum_map: array<string, array<string, string>>,
     *     mapped_rows: array<int, array<string, string>>,
     * } $data
     */
    public function handleMappingComplete(array $data): void
    {
        $this->fieldMap = $data['field_map'];
        $this->enumMap = $data['enum_map'];

        $this->nextStep();
    }

    /**
     * PreviewStep meldet: Backup erstellt, Import kann starten.
     */
    public function handleBackupComplete(string $backupPath): void
    {
        $this->backupPath = $backupPath;
        $this->nextStep();
    }

    /**
     * ImportStep meldet: Import abgeschlossen – zurück zu Step 1.
     */
    public function handleImportComplete(): void
    {
        $this->reset();
        $this->currentStep = 1;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.import.page')
            ->title(__('members.import.page_title'));
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Member\Import\Steps;

use App\Enums\MemberExportType;
use App\Jobs\ProcessMemberZipImport;
use App\Services\Import\MemberCsvParser;
use App\Services\Import\ZipImportHandler;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

final class UploadStep extends Component
{
    use WithFileUploads;

    public string $importType = MemberExportType::STAMMDATEN->value;

    #[Validate]
    public mixed $file = null;

    public ?string $errorMessage = null;

    public bool $zipJobDispatched = false;

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                $this->importType === MemberExportType::FULL->value
                    ? 'mimes:zip'
                    : 'mimetypes:text/csv,text/plain',
                $this->importType === MemberExportType::FULL->value
                    ? 'max:51200'  // 50MB
                    : 'max:10240', // 10MB
            ],
        ];
    }

    public function processFile(): void
    {
        $this->validate();
        $this->errorMessage = null;

        try {
            if ($this->importType === MemberExportType::FULL->value) {
                $this->handleZipUpload();
            } else {
                $this->handleCsvUpload();
            }
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    private function handleCsvUpload(): void
    {
        $result = MemberCsvParser::parse($this->file);

        $this->dispatch('upload-complete', data: [
            'headers' => $result['headers'],
            'all_rows' => $result['all_rows'],
            'total_rows' => $result['total_rows'],
            'import_type' => $this->importType,
        ]);
    }

    private function handleZipUpload(): void
    {
        // ZIP in persistenten Storage speichern – Livewire temp wird gelöscht
        $storedPath = $this->file->storeAs(
            'imports/zip',
            'import_'.now()->format('Y-m-d_His').'_'.str()->uuid().'.zip',
            'local',
        );

        if ($storedPath === false) {
            throw new \RuntimeException('Could not store ZIP file.');
        }

        // Checksum vorab prüfen – schlägt fehl bevor Job dispatched wird
        $absolutePath = Storage::disk('local')->path($storedPath);
        $tmpFile = new \Illuminate\Http\UploadedFile($absolutePath, 'import.zip', 'application/zip', null, true);

        ZipImportHandler::extract($tmpFile); // Wirft Exception bei ungültigem ZIP

        // Job in Queue dispatchen
        /** @var \App\Models\User $user */
        $user = auth()->user();

        ProcessMemberZipImport::dispatch($storedPath, $user->id);

        $this->zipJobDispatched = true;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.import.steps.upload-step');
    }
}

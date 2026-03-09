<?php

declare(strict_types=1);

namespace App\Livewire\App\Global;

use App\Models\Document;
use App\Models\User;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Lazy]
final class Documents extends Component
{
    use WithFileUploads;

    // =========================================================================
    // Props
    // =========================================================================

    /**
     * Das Eltern-Model (Funding, Project, Member, ...).
     * Muss HasDocuments implementieren.
     *
     * @var \Illuminate\Database\Eloquent\Model&\App\Models\Contracts\HasDocuments<\Illuminate\Database\Eloquent\Model>
     */
    public Model $model;

    /** @var class-string */
    public string $categoryEnum;

    public string $policyAction = 'update';

    // =========================================================================
    // Form State
    // =========================================================================

    public bool $showUploadForm = false;

    #[Validate('nullable|string|max:255')]
    public string $label = '';

    #[Validate('nullable|string|max:1000')]
    public string $notes = '';

    #[Validate('nullable|string')]
    public string $category = '';

    /**
     * @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[]
     */
    #[Validate(['files.*' => 'required|file|max:20480'])]
    public array $files = [];

    // =========================================================================
    // Lifecycle
    // =========================================================================

    /** @param class-string $categoryEnum */
    public function mount(Model $model, string $categoryEnum): void
    {
        $this->model = $model;
        $this->categoryEnum = $categoryEnum;
    }

    /** @phpstan-return \Illuminate\View\View */
    public function placeholder(): \Illuminate\Contracts\View\View
    {
        return view('livewire.app.global.documents-placeholder');
    }

    // =========================================================================
    // Upload
    // =========================================================================

    public function storeDocuments(): void
    {
        try {
            $this->authorize($this->policyAction, $this->model);
        } catch (AuthorizationException) {
            Flux::toast(text: __('documents.errors.unauthorized'), variant: 'danger');

            return;
        }

        $this->validate();

        if (empty($this->files)) {
            $this->addError('files', __('documents.errors.no_files'));

            return;
        }

        $categoryInstance = $this->category !== ''
            ? ($this->categoryEnum)::from($this->category)
            : null;

        $uploaded = 0;
        $failed = 0;

        foreach ($this->files as $file) {
            if ($categoryInstance !== null && ! $categoryInstance->isMimeTypeAllowed($file->getMimeType())) {
                $this->addError('files', __('documents.errors.mime_not_allowed'));
                $failed++;

                continue;
            }

            $uuid = Str::uuid()->toString();
            $type = Str::snake(class_basename($this->model::class));
            $dir = "documents/{$type}/{$this->model->getKey()}";
            $path = "{$dir}/{$uuid}";

            try {
                DB::transaction(function () use ($file, $uuid, $path, $dir, $categoryInstance): void {
                    Storage::disk('local')->putFileAs(
                        $dir,
                        $file->getRealPath(),
                        $uuid,
                    );

                    $this->model->documents()->create([
                        'uploaded_by_user_id' => Auth::id(),
                        'uuid' => $uuid,
                        'original_name' => $file->getClientOriginalName(),
                        'disk' => 'local',
                        'path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'category' => $categoryInstance?->value,
                        'label' => $this->label !== '' ? $this->label : null,
                        'notes' => $this->notes !== '' ? $this->notes : null,
                    ]);
                });

                $uploaded++;
            } catch (\Throwable $e) {
                Storage::disk('local')->delete($path);
                $failed++;
                Log::error('Document upload failed', [
                    'model' => $this->model::class,
                    'id' => $this->model->getKey(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->reset('files', 'label', 'notes', 'category', 'showUploadForm');

        if ($uploaded > 0) {
            Flux::toast(
                text: trans_choice('documents.upload_success', $uploaded, ['count' => $uploaded]),
                variant: 'success',
            );
        }

        if ($failed > 0) {
            Flux::toast(
                text: __('documents.upload_partial_failure', ['count' => $failed]),
                variant: 'warning',
            );
        }
    }

    // =========================================================================
    // Download
    // =========================================================================

    public function download(int $documentId): StreamedResponse
    {
        $document = Document::findOrFail($documentId);

        try {
            $this->authorize('view', $this->model);
        } catch (AuthorizationException) {
            Flux::toast(text: __('documents.errors.unauthorized'), variant: 'danger');
            abort(403);
        }

        if (! $document->storageExists()) {
            Flux::toast(text: __('documents.errors.file_not_found'), variant: 'danger');
            abort(404);
        }

        /** @var User $user */
        $user = Auth::user();
        $document->recordAccess($user);

        return Storage::disk($document->disk)->download(
            $document->path,
            $document->original_name,
            [
                'Content-Type' => $document->mime_type,
                'Cache-Control' => 'no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ]
        );
    }

    // =========================================================================
    // Delete
    // =========================================================================

    public function delete(int $documentId): void
    {
        $document = Document::findOrFail($documentId);

        try {
            $this->authorize($this->policyAction, $this->model);
        } catch (AuthorizationException) {
            Flux::toast(text: __('documents.errors.unauthorized'), variant: 'danger');

            return;
        }

        if ($document->storageExists()) {
            Storage::disk($document->disk)->delete($document->path);
        }

        $document->delete();

        Flux::toast(text: __('documents.delete_success'), variant: 'success');
    }

    // =========================================================================
    // Render
    // =========================================================================

    public function render(): \Illuminate\Contracts\View\View
    {
        $documents = Document::query()
            ->where('documentable_type', $this->model::class)
            ->where('documentable_id', $this->model->getKey())
            ->with(['uploadedBy', 'lastAccessedBy'])
            ->latest()
            ->get();

        /** @var User $user */
        $user = Auth::user();

        return view('livewire.app.global.documents', [
            'documents' => $documents,
            'categories' => ($this->categoryEnum)::selectOptions(),
            'canUpload' => $user->can($this->policyAction, $this->model),
            'canDelete' => $user->can($this->policyAction, $this->model),
        ]);
    }
}

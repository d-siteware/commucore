<?php

declare(strict_types=1);

namespace App\Livewire\Member\Show;

use App\Enums\MemberDocumentCategory;
use App\Models\Membership\Member;
use App\Models\Membership\MemberDocument;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public Member $member;

    #[Validate('nullable|string|max:1000')]
    public string $notes = '';

    #[Validate('required')]
    public string $category = '';

    #[Validate('required|file|max:10240|mimes:pdf,jpg,jpeg,png,tif,tiff')]
    public $file = null;

    public bool $showUploadForm = false;

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    public function mount(Member $member): void
    {
        $this->member = $member;
    }

    public function placeholder(): \Illuminate\View\View
    {
        return view('livewire.member.show.documents-placeholder');
    }

    // -------------------------------------------------------------------------
    // Upload
    // -------------------------------------------------------------------------

    public function storeDocument(): void
    {

        try {
            $this->authorize('create', MemberDocument::class);
        } catch (AuthorizationException) {
            Flux::toast(
                text: __('members.documents.errors.unauthorized'),
                heading: __('global.forbidden'),
                variant: 'danger',
            );

            return;
        }

        $this->validate();

        // Kategorie-spezifische MIME-Prüfung (zweite Sicherheitslinie)
        $category = MemberDocumentCategory::from($this->category);

        if (! $category->isMimeTypeAllowed($this->file->getMimeType())) {
            $this->addError('file', __('members.documents.errors.mime_not_allowed_for_category'));

            return;
        }

        $uuid = Str::uuid()->toString();
        $path = 'member-documents/'.$uuid;

        try {
            DB::transaction(function () use ($uuid, $path, $category): void {
                Storage::disk('local')->putFileAs(
                    'member-documents',
                    $this->file->getRealPath(),
                    $uuid
                );

                $this->member->documents()->create([
                    'uploaded_by_user_id' => Auth::id(),
                    'uuid' => $uuid,
                    'original_name' => $this->file->getClientOriginalName(),
                    'disk' => 'private',
                    'path' => $path,
                    'mime_type' => $this->file->getMimeType(),
                    'size' => $this->file->getSize(),
                    'category' => $category,
                    'notes' => $this->notes ?: null,
                ]);
            });
        } catch (\Throwable $e) {
            // Falls DB fehlschlägt, Storage-Datei aufräumen
            Storage::disk('local')->delete($path);

            Flux::toast(
                text: __('members.documents.errors.upload_failed'),
                heading: __('global.error'),
                variant: 'danger',
            );

            return;
        }

        $this->reset('file', 'notes', 'category', 'showUploadForm');

        Flux::toast(
            text: __('members.documents.upload_success'),
            heading: __('global.success'),
            variant: 'success',
        );
    }

    // -------------------------------------------------------------------------
    // Download
    // -------------------------------------------------------------------------

    public function download(int $documentId): StreamedResponse
    {
        $document = MemberDocument::findOrFail($documentId)->load('member');

        try {
            $this->authorize('download', $document);
        } catch (AuthorizationException) {
            Flux::toast(
                text: __('members.documents.errors.unauthorized'),
                heading: __('global.forbidden'),
                variant: 'danger',
            );
            abort(403);
        }

        if (! $document->storageExists()) {
            Flux::toast(
                text: __('members.documents.errors.file_not_found'),
                heading: __('global.error'),
                variant: 'danger',
            );
            abort(404);
        }

        $document->recordAccess(Auth::user());

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

    // -------------------------------------------------------------------------
    // Löschen
    // -------------------------------------------------------------------------

    public function delete(int $documentId): void
    {
        $document = MemberDocument::findOrFail($documentId)->load('member');

        try {
            $this->authorize('delete', $document);
        } catch (AuthorizationException) {
            Flux::toast(
                text: __('members.documents.errors.unauthorized'),
                heading: __('global.forbidden'),
                variant: 'danger',
            );

            return;
        }

        $document->delete();

        Flux::toast(
            text: __('members.documents.delete_success'),
            heading: __('global.success'),
            variant: 'success',
        );
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): \Illuminate\View\View
    {
        $documents = MemberDocument::query()
            ->where('member_id', $this->member->id)
            ->with(['uploadedBy', 'lastAccessedBy'])
            ->latest()
            ->get();

        return view('livewire.member.show.documents', [
            'documents' => $documents,
            'categories' => MemberDocumentCategory::selectOptions(),
            'canUpload' => Auth::user()->can('create', MemberDocument::class),
            'canDelete' => Auth::user()->can('delete', MemberDocument::class),
        ]);
    }
}

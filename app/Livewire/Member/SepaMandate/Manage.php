<?php

declare(strict_types=1);

namespace App\Livewire\Member\SepaMandate;

use App\Enums\MemberDocumentCategory;
use App\Enums\SepaCollectionAttemptStatus;
use App\Enums\SepaMandateType;
use App\Models\Document;
use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;
use App\Models\Sepa\SepaCollectionAttempt;
use App\Services\Sepa\SepaMandateService;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

final class Manage extends Component
{
    use WithFileUploads;

    public Member $member;

    public ?SepaMandate $editing = null;

    public string $iban = '';

    public string $bic = '';

    public string $account_holder = '';

    public string $mandate_type = 'core';

    public array $sepa_documents = [];

    public string $notes = '';

    public bool $showForm = false;

    protected SepaMandateService $mandateService;

    public function boot(SepaMandateService $mandateService): void
    {
        $this->mandateService = $mandateService;
    }

    public function mount(Member $member): void
    {
        $this->member = $member;
    }

    public function rules(): array
    {
        return [
            'iban' => ['required', 'string', 'max:34', new \App\Rules\ValidIban],
            'bic' => ['nullable', 'string', 'max:11', 'regex:/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2,5}$/'],
            'account_holder' => ['required', 'string', 'max:255'],
            'mandate_type' => ['required', Rule::in(SepaMandateType::toArray())],
            'sepa_documents' => ['nullable', 'array'],
            'sepa_documents.*' => ['file', 'mimes:pdf', 'max:10240'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'iban' => __('sepa.mandate.fields.iban'),
            'bic' => __('sepa.mandate.fields.bic'),
            'account_holder' => __('sepa.mandate.fields.account_holder'),
            'mandate_type' => __('sepa.mandate.fields.mandate_type'),
            'sepa_documents.*' => __('sepa.mandate.fields.sepa_documents'),
            'notes' => __('sepa.mandate.fields.notes'),
        ];
    }

    #[On('create-mandate')]
    public function openCreateForm(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(SepaMandate $mandate): void
    {
        $this->editing = $mandate;
        $this->iban = $mandate->iban;
        $this->bic = $mandate->bic ?? '';
        $this->account_holder = $mandate->account_holder;
        $this->mandate_type = $mandate->mandate_type->value;
        $this->notes = $mandate->notes ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->authorize('update', $this->member);
        $this->validate();

        DB::transaction(function () {
            $documentId = null;

            foreach ($this->sepa_documents as $sepaDocument) {
                $path = $sepaDocument->store('member-documents/'.$this->member->id, 'local');
                $doc = Document::create([
                    'documentable_type' => $this->member::class,
                    'documentable_id' => $this->member->id,
                    'uploaded_by_user_id' => auth()->id(),
                    'uuid' => Str::uuid(),
                    'original_name' => $sepaDocument->getClientOriginalName(),
                    'disk' => 'local',
                    'path' => $path,
                    'mime_type' => $sepaDocument->getMimeType(),
                    'size' => $sepaDocument->getSize(),
                    'category' => MemberDocumentCategory::Sepa->value,
                ]);

                $documentId ??= $doc->id;
            }

            if ($this->editing) {
                $this->editing->update([
                    'iban' => $this->iban,
                    'bic' => $this->bic ?: null,
                    'account_holder' => $this->account_holder,
                    'mandate_type' => $this->mandate_type,
                    'signed_document_id' => $documentId ?? $this->editing->signed_document_id,
                    'notes' => $this->notes ?: null,
                ]);
                $mandate = $this->editing;
            } else {
                /** @var SepaMandate|null $oldActive */
                $oldActive = $this->member->activeSepaMandate()->first();

                if ($oldActive) {
                    $hasPending = SepaCollectionAttempt::query()
                        ->where('member_id', $this->member->id)
                        ->unresolved()
                        ->exists();

                    if ($hasPending) {
                        Flux::toast(
                            text: __('sepa.mandate.messages.pending_fees_warning'),
                            variant: 'warning',
                        );
                    }

                    $oldActive->cancel();

                    Flux::toast(
                        text: __('sepa.mandate.messages.replaced'),
                        variant: 'info',
                    );
                }

                $mandate = $this->mandateService->create(
                    member: $this->member,
                    iban: $this->iban,
                    accountHolder: $this->account_holder,
                    bic: $this->bic ?: null,
                    type: SepaMandateType::from($this->mandate_type),
                    signedDocument: $documentId ? Document::find($documentId) : null,
                    notes: $this->notes ?: null,
                );
            }

            $this->member->update([
                'iban' => $this->iban,
                'bic' => $this->bic ?: null,
                'account_holder' => $this->account_holder,
            ]);
        });

        Flux::toast(
            text: $this->editing
                ? __('sepa.mandate.messages.updated')
                : __('sepa.mandate.messages.created'),
            variant: 'success',
        );

        $this->resetForm();
    }

    public function cancel(SepaMandate $mandate): void
    {
        $this->authorize('update', $this->member);

        $hasPending = SepaCollectionAttempt::query()
            ->where('member_id', $this->member->id)
            ->unresolved()
            ->exists();

        if ($hasPending) {
            Flux::toast(
                text: __('sepa.mandate.messages.pending_fees_warning'),
                variant: 'warning',
            );
        }

        $this->mandateService->cancel($mandate);

        Flux::toast(
            text: __('sepa.mandate.messages.cancelled'),
            variant: 'success',
        );
    }

    public function downloadDocument(Document $document): mixed
    {
        $this->authorize('view', $this->member);

        if (! $document->storageExists()) {
            Flux::toast(
                text: __('documents.errors.file_not_found'),
                variant: 'danger',
            );
            abort(404);
        }

        $document->recordAccess(auth()->user());

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

    public function resetForm(): void
    {
        $this->editing = null;
        $this->iban = '';
        $this->bic = '';
        $this->account_holder = '';
        $this->mandate_type = 'core';
        $this->sepa_documents = [];
        $this->notes = '';
        $this->showForm = false;
    }

    public function render(): mixed
    {
        /** @var \Illuminate\Support\Collection<int, SepaMandate> $mandates */
        $mandates = $this->member->sepaMandates()
            ->with('signedDocument')
            ->latest()
            ->get();

        return view('livewire.member.sepa-mandate.manage', [
            'mandates' => $mandates,
            'activeMandate' => $mandates->first(fn (SepaMandate $m) => $m->isUsable()),
        ]);
    }
}

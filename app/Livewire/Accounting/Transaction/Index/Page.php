<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Transaction\Index;

use App\Actions\Accounting\AppendEventTransaction;
use App\Actions\Accounting\AppendFundingTransaction;
use App\Actions\Accounting\AppendMemberTransaction;
use App\Actions\Accounting\AppendProjectTransaction;
use App\Actions\Accounting\TransferTransaction;
use App\Enums\DateRange;
use App\Enums\Gender;
use App\Enums\TransactionDocumentCategory;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Helpers\MoneyHelper;
use App\Livewire\Forms\Accounting\EditTextTransactionForm;
use App\Livewire\Forms\Accounting\ReceiptForm;
use App\Livewire\Forms\Accounting\TransferTransactionForm;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\Sortable;
use App\Mail\TransactionReceiptMail;
use App\Models\Accounting\Account;
use App\Models\Accounting\Receipt;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Event\EventTransaction;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingTransaction;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use App\Models\Project\Project;
use App\Models\Project\ProjectTransaction;
use App\Services\MemberInvoiceService;
use Carbon\Carbon;
use Exception;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class Page extends Component
{
    use HasPrivileges;
    use Sortable;
    use WithFileUploads;
    use WithPagination;

    protected $listeners = ['transaction-updated'];

    /**
     * Mehrere Dateien gleichzeitig.
     *
     * @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile[]
     */
    #[Validate(['documentFiles.*' => 'file|max:20480|mimes:pdf,jpg,jpeg,png,tif,tiff,doc,docx,xls,xlsx'])]
    public array $documentFiles = [];

    #[Validate('nullable|string|max:255')]
    public string $documentLabel = '';

    #[Validate('nullable|string')]
    public string $documentCategory = '';

    public ReceiptForm $receipt;

    public ?Transaction $transaction = null;

    public EditTextTransactionForm $edit_text_form;

    public TransferTransactionForm $transfer_transaction_form;

    #[Url]
    public string $search = '';

    #[Url]
    public array $filter_status = [];

    public array $filter_type = [];

    public $selectedTransactions = [];

    public $numTransactions;

    public $transactionsOnPage = [];

    public $allTransactions = [];

    public $filter_date_range = DateRange::All->value;

    #[Validate]
    public $target_event;

    #[Validate]
    public $target_member;

    public $event_visitor_name;

    public $event_gender;

    public $transfer_account_id;

    public $transfer_transaction_id;

    public $transfer_reason;

    public $selectedRow;

    public $pdfBase64; // Property to hold the base64-encoded PDF

    public $showPreviewModal = false; // Property to control the modal visibility

    public $previewUrl;

    public bool $is_membership_fee = false;

    public int $fee_year;

    public ?int $target_project = null;

    public ?string $target_project_allocated = null;

    public ?int $target_funding = null;

    public ?string $target_funding_allocated = null;

    public function sendInvoice($transactionId): void
    {
        try {
            $transaction = Transaction::with('member_transaction.member')
                ->findOrFail($transactionId);
            $member = $transaction->member_transaction->member ?? null;

            $getMemberTransaction = MemberTransaction::query()
                ->where('member_id', $member->id)
                ->where('transaction_id', $transaction->id)
                ->first();

            $invoiceService = new MemberInvoiceService;
            $pdfContent = $invoiceService->generate($transaction, $member, app()->getLocale());

            if ($member && ! empty($member->email)) { // Updated condition

                $filename = storage_path('app/invoices/Quittung_#'.Str::padLeft('Q'.$transaction->id, 6, '0').'.pdf');
                if (! file_exists(dirname($filename))) {
                    mkdir(dirname($filename), 0755, true);
                }
                file_put_contents($filename, $pdfContent);

                try {
                    Mail::to($member->email)
                        ->locale($member->locale)
                        ->send(new TransactionReceiptMail($member, $filename, $transaction));
                    unlink($filename);
                    Flux::toast('Rechnung wurde erfolgreich an '.$member->email.' gesendet.', 'Erfolg');
                    $getMemberTransaction->receipt_sent_timestamp = Carbon::now('Europe/Berlin');
                    $getMemberTransaction->save();
                    $this->dispatch('transaction-updated');
                } catch (Exception $e) {
                    if (file_exists($filename)) {
                        unlink($filename);
                    }
                    Flux::toast('Rechnung wurde erfolgreich an '.$member->email.' gesendet.', 'Fehler');
                    $this->addError('email', 'Fehler beim Senden der Rechnung: '.$e->getMessage());
                }
            } else {
                Flux::toast('Die Rechnung kann nicht versendet werden, da das Mitglied keine E-Mail-Adresse hat. Bitte diese einpflegen oder ausdrucken und per Post senden.', 'Fehler', 9000, 'warning');
            }
        } catch (Exception $e) {
            Log::error('Error in sendInvoice: '.$e->getMessage()."\nStack trace: ".$e->getTraceAsString());
        }
    }

    public function closePreviewModal(): void
    {
        $this->showPreviewModal = false;
        $this->pdfBase64 = null; // Clear the base64 data to free memory
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function transactions(): LengthAwarePaginator
    {
        $this->allTransactions = Transaction::all()
            ->map(fn ($transaction): string => (string) $transaction->id)
            ->toArray();

        $date_range = DateRange::from($this->filter_date_range)
            ->dates();

        $transactionList = Transaction::query()
            ->with(['event_transaction', 'member_transaction', 'project_transaction', 'funding_transaction', 'account'])
            ->whereYear('date', session('financialYear'))
            ->tap(fn ($query) => $this->search ? $query->where('label', 'LIKE', '%'.$this->search.'%') : $query)
            ->whereIn('status', $this->filter_status)
            ->whereIn('type', $this->filter_type)
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->tap(fn ($query) => $this->filter_date_range === DateRange::All->value ? $query : $query->whereBetween('date', $date_range))
//            ->tap(fn($query) => logger()->info($query->toSql(), $query->getBindings()))
            ->paginate(15)
            ->through(fn ($transaction) => $transaction->refresh());

        $this->transactionsOnPage = $transactionList->map(fn ($transaction): string => (string) $transaction->id)
            ->toArray();

        return $transactionList;
    }

    #[Computed]
    public function selectedFundingRemaining(): ?int
    {
        if (! $this->target_funding || ! $this->transaction) {
            return null;
        }

        $funding = Funding::find($this->target_funding);

        if (! $funding) {
            return null;
        }

        return min(
            $this->transaction->amount_gross,
            $funding->remainingAmount(),
        );
    }

    #[Computed]
    public function selectedProjectMaxAmount(): ?int
    {
        if (! $this->target_project || ! $this->transaction) {
            return null;
        }

        // Projekte haben kein eigenes Budget – Max ist der Buchungsbetrag
        return $this->transaction->amount_gross;
    }

    #[Computed]
    public function documentCategories(): array
    {
        return TransactionDocumentCategory::selectOptions();
    }

    public function mount(): void
    {
        $this->filter_status = TransactionStatus::toArray();
        $this->filter_type = TransactionType::toArray();
        $this->fee_year = (int) session('financialYear');
    }

    public function download(int $receipt_id): StreamedResponse
    {
        $receipt = Receipt::findOrFail($receipt_id);

        $filePath = "accounting/receipts/{$receipt->file_name}";

        // Debugging: Check if the file exists
        if (! Storage::disk('local')
            ->exists($filePath)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')
            ->download($filePath, $receipt->file_name_original);
    }

    public function bookItem(int $transaction_id): void
    {
        $this->authorize('book-item', Account::class);
        $this->dispatch('book-transaction', transactionId: $transaction_id);
        $this->transaction = Transaction::find($transaction_id);
        Flux::modal('book-transaction')
            ->show();
    }

    public function editItem(int $transaction_id): void
    {
        $this->authorize('update', Account::class);
        $this->dispatch('edit-transaction', transactionId: $transaction_id);
        $this->transaction = Transaction::find($transaction_id);

        Flux::modal('edit-transaction')
            ->show();
    }

    public function detachMember(int $member_transaction_id): void
    {
        $this->checkPrivilege(Transaction::class);
        MemberTransaction::findOrFail($member_transaction_id)
            ->delete();
        Flux::toast(
            text: __('transaction.detach-member-success.text'),
            heading: __('transaction.detach-member-success.heading'),
            variant: 'success',
        );
    }

    public function detachEvent(int $event_transaction_id): void
    {
        $this->checkPrivilege(Transaction::class);
        if (EventTransaction::findOrFail($event_transaction_id)
            ->delete()) {
            Flux::toast(
                text: __('transaction.detach-event-success.text'),
                heading: __('transaction.detach-event-success.heading'),
                variant: 'success',
            );
        }
    }

    public function appendToEvent(int $transaction_id): void
    {
        $this->checkPrivilege(Transaction::class);

        $this->transaction = Transaction::findOrFail($transaction_id);
        Flux::modal('append-to-event-transaction')
            ->show();
    }

    public function appendToMember(int $transaction_id): void
    {
        $this->checkPrivilege(Transaction::class);

        $this->transaction = Transaction::findOrFail($transaction_id);
        Flux::modal('append-to-member-transaction')
            ->show();
    }

    public function appendEvent(): void
    {
        $this->checkPrivilege(Transaction::class);

        $this->validate([
            'transaction.id' => ['unique:event_transactions,transaction_id'],
            'target_event' => 'required',
            'event_visitor_name' => '',
            'event_gender' => ['nullable', Rule::enum(Gender::class)],
        ], [
            'target_event.required' => 'Bitte eine Veranstaltung auswählen',
            'transaction.id.unique' => 'Buchung ist bereits der Veranstaltung zugeordnnet worden',
        ]);

        $event = Event::findOrFail($this->target_event);

        AppendEventTransaction::handle($this->transaction, $event, $this->event_visitor_name, $this->event_gender);
        Flux::toast(
            text: 'Die Buchung wurde erfolgreich zugeordnet',
            heading: __('transaction.attach-event-success.heading'),
            variant: 'success',
        );
        Flux::modal('append-to-event-transaction')
            ->close();
    }

    public function appendMember(): void
    {
        $this->checkPrivilege(Transaction::class);
        $this->validate([
            'transaction.id' => ['unique:member_transactions,transaction_id'],
            'target_member' => 'required',
            'fee_year' => 'nullable|integer|min:2010',
        ], [
            'target_member.required' => 'Bitte ein Mitglied auswählen',
            'transaction.id.unique' => 'Buchung ist bereits einem Mitglied zugeordnet worden',
            'fee_year.integer' => 'Buchungen dürfen nicht älter als 2010 sein',
        ]);

        $member = Member::findOrFail($this->target_member);

        AppendMemberTransaction::handle($this->transaction, $member, $this->is_membership_fee, $this->fee_year);

        Flux::toast(
            text: 'Die Buchung wurde erfolgreich zugeordnet',
            heading: __('transaction.detach-event-success.heading'),
            variant: 'success',
        );
        Flux::modal('append-to-member-transaction')
            ->close();
    }

    public function editTransactionText(int $transaction_id): void
    {
        $this->transaction = Transaction::findOrFail($transaction_id);

        $this->edit_text_form->set($this->transaction);

        Flux::modal('edit-transaction-text')
            ->show();
    }

    public function changeTransactionText(): void
    {
        $this->checkPrivilege(Transaction::class);

        if ($this->edit_text_form->update()) {
            Flux::toast(
                text: __('transaction.edit-text-modal.update-success.text'),
                heading: __('transaction.edit-text-modal.update-success.heading'),
                variant: 'success',
            );
            Flux::modal('edit-transaction-text')
                ->close();
        }
    }

    public function startCancelItem(int $transaction_id): void
    {
        $this->transaction = Transaction::find($transaction_id);
        $this->checkPrivilege(Transaction::class);
        $this->dispatch('cancel-transaction', transactionId: $transaction_id);
        Flux::modal('cancel-transaction')
            ->show();
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        $this->checkPrivilege(Transaction::class);
        $this->transaction = $transaction;
        $this->transaction->documents()->delete();
        $this->transaction->delete();
        $this->transaction = null;
        Flux::toast(
            text: __('transaction.delete.success.msg'),
            heading: __('transaction.delete.success.heading'),
            variant: 'success',
        );
    }

    public function confirmTransactionDeletion(Transaction $transaction): void
    {
        $this->checkPrivilege(Transaction::class);
        $this->transaction = $transaction;
        $doc = $this->transaction->documents->count();

        if ($doc > 0) {
            Flux::modal('delete-transaction-confirmation-modal')
                ->show();
        } else {
            $this->deleteTransaction($transaction);
        }

    }

    public function changeAccount(int $transaction_id): void
    {
        $this->checkPrivilege(Transaction::class);
        $this->transaction = Transaction::find($transaction_id);
        $this->transfer_transaction_form->set($this->transaction);
        Flux::modal('account-transfer-modal')
            ->show();
    }

    public function transferAccount(): void
    {
        $this->checkPrivilege(Transaction::class);

        $this->validate([
            'transfer_transaction_form.transaction_id' => ['required', Rule::exists('transactions', 'id')],
            'transfer_transaction_form.account_id' => ['required', Rule::notIn([$this->transaction->account_id])],
            'transfer_transaction_form.reason' => 'required',
        ], [
            'transfer_transaction_form.transaction_id.required' => __('transaction.account-transfer-modal.error.transaction_id'),
            'transfer_transaction_form.account_id.required' => __('transaction.account-transfer-modal.error.account_id'),
            'transfer_transaction_form.account_id.not_in' => __('transaction.account-transfer-modal.error.identical'),
            'transfer_transaction_form.reason.required' => __('transaction.account-transfer-modal.error.reason'),
        ]);

        TransferTransaction::handle($this->transaction, $this->transfer_transaction_form);

        $this->dispatch('transaction-updated');
        Flux::toast(
            text: 'Die Buchung '.$this->transaction->label.' wurde geändert',
            heading: 'Erfolg',
            variant: 'success',
        );
    }

    public function appendToProject(int $transaction_id): void
    {
        $this->checkPrivilege(Transaction::class);

        $this->transaction = Transaction::findOrFail($transaction_id);
        $this->target_project = null;
        $this->target_project_allocated = null;

        Flux::modal('append-to-project-transaction')->show();
    }

    public function appendProject(): void
    {
        $this->checkPrivilege(Transaction::class);

        $this->validate([
            'transaction.id' => ['unique:project_transactions,transaction_id'],
            'target_project' => ['required', 'integer', 'exists:projects,id'],
            'target_project_allocated' => ['nullable', 'string'],
        ], [
            'target_project.required' => 'Bitte ein Projekt auswählen.',
            'transaction.id.unique' => 'Diese Buchung ist bereits einem Projekt zugeordnet.',
        ]);

        $cents = MoneyHelper::toCents($this->target_project_allocated);

        if ($cents !== null && $cents > $this->transaction->amount_gross) {
            Flux::toast(
                text: __('transaction.index.modal.append_project.error.exceeds_amount', [
                    'amount' => MoneyHelper::formatCents($this->transaction->amount_gross),
                ]),
                variant: 'danger',
            );

            return;
        }

        if ($cents !== null && $cents <= 0) {
            Flux::toast(text: 'Bitte einen gültigen Betrag eingeben.', variant: 'danger');

            return;
        }

        $project = Project::findOrFail($this->target_project);

        AppendProjectTransaction::handle(
            $this->transaction,
            $project,
            $cents,
        );

        Flux::toast(
            text: __('transaction.attach-project-success.text'),
            heading: __('transaction.attach-project-success.heading'),
            variant: 'success',
        );

        Flux::modal('append-to-project-transaction')->close();
        $this->reset(['target_project', 'target_project_allocated']);
    }

    public function detachProject(int $project_transaction_id): void
    {
        $this->checkPrivilege(Transaction::class);

        ProjectTransaction::findOrFail($project_transaction_id)->delete();

        Flux::toast(
            text: __('transaction.detach-project-success.text'),
            heading: __('transaction.detach-project-success.heading'),
            variant: 'success',
        );
    }

    public function appendToFunding(int $transaction_id): void
    {
        $this->checkPrivilege(Transaction::class);

        $this->transaction = Transaction::findOrFail($transaction_id);
        $this->target_funding = null;
        $this->target_funding_allocated = null;

        Flux::modal('append-to-funding-transaction')->show();
    }

    public function appendFunding(): void
    {
        $this->checkPrivilege(Transaction::class);

        $this->validate([
            'transaction.id' => ['unique:funding_transactions,transaction_id'],
            'target_funding' => ['required', 'integer', 'exists:fundings,id'],
            'target_funding_allocated' => ['nullable', 'string'],
        ], [
            'target_funding.required' => 'Bitte eine Förderung auswählen.',
            'transaction.id.unique' => 'Diese Buchung ist bereits einer Förderung zugeordnet.',
        ]);

        $funding = Funding::findOrFail($this->target_funding);

        $cents = MoneyHelper::toCents($this->target_funding_allocated);

        if ($cents !== null && $cents > $this->transaction->amount_gross) {
            Flux::toast(
                text: __('transaction.index.modal.append_funding.error.exceeds_amount', [
                    'amount' => MoneyHelper::formatCents($this->transaction->amount_gross),
                ]),
                variant: 'danger',
            );

            return;
        }

        if ($cents !== null && $cents <= 0) {
            Flux::toast(text: 'Bitte einen gültigen Betrag eingeben.', variant: 'danger');

            return;
        }

        AppendFundingTransaction::handle(
            $this->transaction,
            $funding,
            $cents,
        );

        Flux::toast(
            text: __('transaction.attach-funding-success.text'),
            heading: __('transaction.attach-funding-success.heading'),
            variant: 'success',
        );

        Flux::modal('append-to-funding-transaction')->close();
        $this->reset(['target_funding', 'target_funding_allocated']);
    }

    public function detachFunding(int $funding_transaction_id): void
    {
        $this->checkPrivilege(Transaction::class);

        FundingTransaction::findOrFail($funding_transaction_id)->delete();

        Flux::toast(
            text: __('transaction.detach-funding-success.text'),
            heading: __('transaction.detach-funding-success.heading'),
            variant: 'success',
        );
    }

    public function attachDocs(int $transactionId): void
    {
        $this->checkPrivilege(Transaction::class);
        $this->transaction = Transaction::findOrFail($transactionId);
        Flux::modal('upload-transaction-document')->show();
    }

    public function attachDocument(): void
    {
        $this->checkPrivilege(Transaction::class);
        $this->storeDocuments($this->transaction);
    }

    protected function storeDocuments(Transaction $transaction): void
    {
        if (empty($this->documentFiles)) {
            return;
        }

        $categoryInstance = $this->documentCategory !== ''
            ? TransactionDocumentCategory::from($this->documentCategory)
            : null;

        $failed = 0;

        foreach ($this->documentFiles as $file) {
            if ($categoryInstance !== null && ! $categoryInstance->isMimeTypeAllowed($file->getMimeType())) {
                $failed++;

                continue;
            }

            $uuid = Str::uuid()->toString();
            $dir = "documents/transaction/{$transaction->id}";
            $path = "{$dir}/{$uuid}";

            try {
                DB::transaction(function () use ($file, $uuid, $path, $dir, $categoryInstance, $transaction): void {
                    Storage::disk('local')->putFileAs($dir, $file->getRealPath(), $uuid);

                    $transaction->documents()->create([
                        'uploaded_by_user_id' => Auth::id(),
                        'uuid' => $uuid,
                        'original_name' => $file->getClientOriginalName(),
                        'disk' => 'local',
                        'path' => $path,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'category' => $categoryInstance?->value,
                        'label' => $this->documentLabel !== '' ? $this->documentLabel : null,
                    ]);
                });
            } catch (Throwable $e) {
                Storage::disk('local')->delete($path);
                $failed++;
                \Illuminate\Support\Facades\Log::error('Document upload failed on transaction create', [
                    'transaction_id' => $transaction->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $uploaded = count($this->documentFiles) - $failed;

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
        Flux::modal('upload-transaction-document')->close();

        $this->reset('documentFiles', 'documentLabel', 'documentCategory');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.accounting.transaction.index.page')
            ->title(__('transaction.index.title'));
    }
}

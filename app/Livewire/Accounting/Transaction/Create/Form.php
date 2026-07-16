<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Transaction\Create;

use App\Actions\Accounting\CreateEventTransaction;
use App\Actions\Accounting\CreateMemberTransaction;
use App\Actions\Accounting\CreateTransaction;
use App\Actions\Accounting\UpdateTransaction;
use App\Enums\Gender;
use App\Enums\TransactionDocumentCategory;
use App\Enums\TransactionType;
use App\Livewire\Accounting\Transaction\Index\Page;
use App\Livewire\Forms\Accounting\AccountForm;
use App\Livewire\Forms\Accounting\BookingAccountForm;
use App\Livewire\Forms\Accounting\TransactionForm;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Accounting\Account;
use App\Models\Accounting\BookingAccount;
use App\Models\Accounting\FiscalYear;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use App\Models\Membership\Member;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Throwable;

final class Form extends Component
{
    use HasPrivileges;
    use WithFileUploads;

    // =========================================================================
    // Props
    // =========================================================================

    public Event $event;

    public $visitor_name;

    public $gender = Gender::ma;

    public $visitor_has_member_id;

    public ?string $external_visitor_name;

    public ?Member $member;

    public ?TransactionForm $form;

    public ?AccountForm $account;

    public ?BookingAccountForm $booking;

    public ?Transaction $transaction = null;

    public ?Member $selectedMember;

    public ?int $entry_fee;

    public ?int $entry_fee_discounted;

    public ?array $visitors = [];

    private bool $suppressAreaReset = false;

    public bool $check_form = false;

    // =========================================================================
    // Dokumente (ersetzt ReceiptForm)
    // =========================================================================

    /**
     * Mehrere Dateien gleichzeitig.
     *
     * @var TemporaryUploadedFile[]
     */
    #[Validate(['documentFiles.*' => 'file|max:20480|mimes:pdf,jpg,jpeg,png,tif,tiff,doc,docx,xls,xlsx'])]
    public array $documentFiles = [];

    #[Validate('nullable|string|max:255')]
    public string $documentLabel = '';

    #[Validate('nullable|string')]
    public string $documentCategory = '';

    // =========================================================================
    // Listeners
    // =========================================================================

    protected $listeners = ['edit-transaction' => 'loadTransaction'];

    // =========================================================================
    // Computed
    // =========================================================================

    #[Computed]
    public function accounts(): Collection
    {
        return Account::query()->select('id', 'name')->get();
    }

    #[Computed]
    public function booking_accounts(): Collection
    {
        $typeId = FiscalYear::getActive()?->booking_account_type_id;

        return BookingAccount::query()
            ->select('id', 'label', 'number', 'area')
            ->when(
                $this->form->area !== null,
                fn ($q) => $q->where('area', $this->form->area->value)
            )
            ->when($typeId !== null, fn ($q) => $q->where('booking_account_type_id', $typeId))
            ->orderBy('number')
            ->get();
    }

    #[Computed]
    public function documentCategories(): array
    {
        return TransactionDocumentCategory::selectOptions();
    }

    // =========================================================================
    // Lifecycle
    // =========================================================================

    public function mount(?int $transactionId = null): void
    {
        if ($transactionId !== null) {
            $this->transaction = Transaction::query()->find($transactionId);
            if ($this->transaction) {
                $this->form->set($this->transaction);
            }
        } else {
            $this->resetTransactionForm();
        }

        if (isset($this->event)) {
            $this->entry_fee = $this->event->entry_fee;
            $this->entry_fee_discounted = $this->event->entry_fee_discounted;
        }
    }

    public function loadTransaction(int $transactionId): void
    {
        $this->transaction = Transaction::query()->find($transactionId);
        $this->form->set($this->transaction);
    }

    // =========================================================================
    // Submit-Methoden
    // =========================================================================

    public function submitTransaction(): void
    {
        $this->checkPrivilege(Transaction::class);
        $this->form->validate();

        $this->transaction = $this->handleTransaction();

        $this->storeDocuments($this->transaction);

        $this->dispatch('updated-payments');
        $this->redirect(Page::class, true);
    }

    public function submitEventTransaction(): void
    {
        $this->checkPrivilege(Transaction::class);
        $transaction = $this->handleEventTransaction();

        if ($transaction) {
            $this->storeDocuments($transaction);
        }

        if ($this->visitor_has_member_id) {
            $this->handleMemberTransaction($this->form, Member::query()->find($this->visitor_has_member_id));
        }
    }

    public function submitMemberTransaction(): void
    {
        $this->checkPrivilege(Transaction::class);
        $transaction = $this->handleMemberTransaction($this->form, $this->member);

        if ($transaction) {
            $this->storeDocuments($transaction);
        }
    }

    // =========================================================================
    // Dokumente speichern
    // =========================================================================

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
                Log::error('Document upload failed on transaction create', [
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

        $this->reset('documentFiles', 'documentLabel', 'documentCategory');
    }

    // =========================================================================
    // Interne Helfer
    // =========================================================================

    protected function handleTransaction(): Transaction
    {
        if ($this->transaction !== null) {
            UpdateTransaction::handle($this->form);
            Flux::toast(
                text: __('transaction.update-success.text', ['label' => $this->transaction->label]),
                heading: __('transaction.update-success.heading'),
                variant: 'success',
            );
        } else {
            $this->transaction = CreateTransaction::handle($this->form);
            Flux::toast(
                text: __('transaction.create-success.text', ['label' => $this->transaction->label]),
                heading: __('transaction.create-success.heading'),
                variant: 'success',
            );
        }

        return $this->transaction;
    }

    protected function handleEventTransaction(): ?Transaction
    {
        $this->validate([
            'form.account_id' => ['required', 'doesnt_start_with:new'],
            'transaction.id' => 'unique:event_transactions,transaction_id',
            'event' => 'required',
        ], [
            'form.account_id.required' => __('transaction.validation.event.account_id.required'),
            'form.account_id.doesnt_start_with' => __('transaction.validation.event.account_id.doesnt_start_with'),
            'event.required' => 'Event is required.',
            'transaction.id.unique' => 'Diese Buchung wurde bereits vergeben.',
        ]);

        try {
            $transaction = CreateEventTransaction::handle($this->form, $this->event);

            Flux::toast(
                text: __('transaction.event-create-success.text'),
                heading: __('transaction.event-create-success.heading'),
                variant: 'success',
            );
            Flux::modal('add-new-payment')->close();
            $this->dispatch('updated-payments');

            return $transaction;
        } catch (Throwable $e) {
            Flux::toast(
                text: __('transaction.create-error.text', ['message' => $e->getMessage()]),
                heading: __('transaction.create-error.heading'),
                duration: 0,
                variant: 'error',
            );
            Log::error('Transaction creation failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    protected function handleMemberTransaction(TransactionForm $form, Member $member): ?Transaction
    {
        $this->validate([
            'form.account_id' => ['required', 'doesnt_start_with:new'],
            'form.amount_gross' => 'required',
            'form.label' => 'required',
            'transaction.id' => 'unique:member_transactions,transaction_id',
        ], [
            'form.account_id.required' => __('transaction.validation.member.account_id.required'),
            'form.label.required' => __('transaction.validation.member.label.required'),
            'form.amount_gross.required' => __('transaction.validation.member.amount_gross.required'),
        ]);

        try {
            $transaction = CreateMemberTransaction::handle($form, $member);

            Flux::toast(
                text: __('transaction.member-create-success.text'),
                heading: __('transaction.member-create-success.heading'),
                variant: 'success',
            );
            Flux::modal('add-new-payment')->close();
            $this->dispatch('updated-payments');

            return $transaction;
        } catch (Throwable $e) {
            Flux::toast(
                text: __('transaction.create-error.text', ['message' => $e->getMessage()]),
                heading: __('transaction.create-error.heading'),
                duration: 0,
                variant: 'error',
            );
            Log::error('Transaction creation failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    // =========================================================================
    // Account-Helfer
    // =========================================================================

    public function updatedSelectedMember($value): void
    {
        if ($value === 'extern') {
            $this->visitor_name = '';
            $this->visitor_has_member_id = false;
        } else {
            $member = Member::query()->find($value);
            $this->visitor_name = $member ? $member->fullName() : '';
            $this->visitor_has_member_id = $value;
        }
    }

    public function resetTransactionForm(): void
    {
        $this->form->reset();
        $this->form->type = TransactionType::Withdrawal;
        $this->form->vat = 19;
        $this->form->date = now()->format('Y-m-d');
    }

    public function addAccount(): void
    {
        $this->checkPrivilege(Transaction::class);
        $this->account->create();
        $this->form->account_id = $this->account->id;
    }

    public function createAccount(): void
    {
        $this->checkPrivilege(Transaction::class);
        $this->account->create();
        $this->reset('account');
    }

    public function addBookingAccount(): void
    {
        $this->checkPrivilege(Transaction::class);
        $this->booking->create();
        $this->form->booking_account_id = $this->booking->id;
    }

    public function createBookingAccount(): void
    {
        $this->checkPrivilege(Transaction::class);
        $this->booking->create();
        $this->reset('booking');
    }

    public function addVisitor(): void
    {
        $this->visitors[] = $this->visitor_name;
    }

    public function updatedFormBookingAccountId(mixed $value): void
    {
        if (! $value || $value === 'new') {
            return;
        }

        $account = BookingAccount::find($value);
        if ($account && $this->form->area === null) {
            $this->suppressAreaReset = true;
            $this->form->area = $account->area;
            $this->suppressAreaReset = false;
        }
    }

    public function updatedFormArea(): void
    {
        if ($this->suppressAreaReset) {
            return;
        }

        if (! $this->form->booking_account_id) {
            return;
        }

        $account = BookingAccount::find($this->form->booking_account_id);
        if ($account && $account->area !== $this->form->area) {
            $this->form->booking_account_id = null;
            Flux::toast(
                text: __('transaction.area-reset-warning.text'),
                variant: 'warning',
            );
        }
    }

    // =========================================================================
    // Render
    // =========================================================================

    public function render(): View
    {
        return view('livewire.accounting.transaction.create.form');
    }
}

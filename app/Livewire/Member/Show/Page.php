<?php

declare(strict_types=1);

namespace App\Livewire\Member\Show;

use App\Livewire\Forms\Member\MemberForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\PersistsTabs;
use App\Livewire\Traits\Sortable;
use App\Mail\InvitationMail;
use App\Models\Accounting\Account;
use App\Models\Accounting\Receipt;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Invitation;
use App\Models\Membership\Member;
use App\Models\Membership\MemberApplication;
use App\Models\Membership\MemberTransaction;
use App\Models\User;
use App\Notifications\MemberRejectedNotification;
use Flux\Flux;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class Page extends Component
{
    use HandlesErrors;
    use HasPrivileges;
    use PersistsTabs;
    use Sortable;
    use WithPagination;

    /** @var Collection<int, User> */
    public $users;

    public int $newUser = 0;

    public ?Member $member;

    public ?Member $cancelMyMembership = null;

    public MemberForm $memberForm;

    public string $confirm_deletion_text = '';

    public bool $hasUser = false;

    /** @var array<string, mixed> */
    protected array $feeStatusResults = [];

    public string $openFees = '';

    public string $feeStatus = '';

    public string $searchPayment = '';

    public ?Transaction $transaction = null;

    public ?string $defaultTab = 'member-show-profile';

    public string $selectedTab = '';

    public ?int $applicationId;

    public ?string $invitation_status = null;

    protected $listeners = ['updated-payments' => 'payments', 'membershipAccepted'];

    public ?string $fee_type = null;

    #[Computed]
    public function payments(): LengthAwarePaginator
    {
        return MemberTransaction::query()
            ->where('member_id', '=', $this->member->id)
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->tap(fn ($query) => $this->searchPayment !== '' && $this->searchPayment !== '0' ? $query->whereHas('transaction', function ($query): void {
                $query->where('label', 'LIKE', '%'.$this->searchPayment.'%')
                    ->orWhere('reference', 'LIKE', '%'.$this->searchPayment.'%')
                    ->orWhere('description', 'LIKE', '%'.$this->searchPayment.'%');
            }) : $query)
            ->paginate(10);
    }

    public function mount(Member $member): void
    {
        try {
            $this->authorize('view', $member);
        } catch (AuthorizationException $e) {
            Flux::toast($e->getMessage(), 'error');
            Log::alert('Unberechtigter Zugriffsversuch ', [
                'Mitglied' => $member,
                'User' => Auth::user() ?? 'extern',
                'msg' => $e->getMessage(),
            ]);
            $this->redirect(route('backend.members.index'), true);
        }
        $this->selectedTab = $this->getSelectedTab();
        $this->memberForm->set($member);
        $this->users = User::select('id', 'name')->get();

        $this->invitation_status = $member->checkInvitationStatus();

        $this->feeStatusResults = $member->feeStatus();

        $this->feeStatus = (string) $this->feeStatusResults['status'];
        $this->openFees = \App\Helpers\MoneyHelper::formatCents((int) ($this->feeStatusResults['paid'] * 100), withSymbol: false);

        $this->fee_type = $this->memberForm->fee_type;
    }

    public function updated(string $propertyName): void
    {
        if (str_starts_with($propertyName, 'memberForm.')) {
            $this->memberForm->validateOnly(substr($propertyName, 11));
        }
    }

    public function detachUser(int $userid): void
    {
        try {
            if ($this->memberForm->user_id === $userid) {
                $this->memberForm->user_id = null;
                if ($this->member->save()) {
                    $this->hasUser = false;
                    Flux::toast(
                        text: __('members.show.detached.success.msg', ['name' => $this->member->name]),
                        heading: __('members.show.detached.success.head'),
                        variant: 'success',
                    );
                }
            }
        } catch (\Throwable $e) {
            $this->handleError('Benutzer-Verknüpfung lösen fehlgeschlagen', $e);
        }
    }

    public function attachUser(): void
    {
        try {
            if ($this->newUser > 0) {
                $getUser = User::find($this->newUser);
                if ($getUser instanceof User && $getUser->id === $this->newUser) {
                    $this->memberForm->member->user_id = $this->newUser;
                    if ($this->memberForm->member->save()) {
                        $this->hasUser = true;

                        Flux::toast(
                            text: __('members.show.attached.success.msg', ['name' => $getUser->name]),
                            heading: __('members.show.attached.success.head'),
                            variant: 'success',
                        );
                        $this->memberForm->user_id = $this->newUser;
                    }
                } else {
                    Flux::toast(
                        text: __('members.backend.attach.failed.msg'),
                        heading: __('members.backend.attach.failed.head'),
                        variant: 'danger',
                    );
                }
            }
        } catch (\Throwable $e) {
            $this->handleError('Benutzer-Verknüpfung fehlgeschlagen', $e);
        }
    }

    public function updateMemberData(): void
    {
        try {
            $this->checkPrivilege(Member::class);

            if ($this->memberForm->updateData()) {
                Flux::toast(
                    text: __('members.update.success.content'),
                    heading: __('members.update.success.title'),
                    variant: 'success',
                );
            }
        } catch (\Throwable $e) {
            $this->handleError('Mitgliedsdaten aktualisieren fehlgeschlagen', $e);
        }
    }

    public function sendInvitation(): void
    {
        try {
            $this->validate([
                'memberForm.email' => 'required|email|unique:invitations,email|unique:users,email',
            ]);

            $invitation = Invitation::create([
                'email' => $this->memberForm->email,
                'token' => Str::random(32),
            ]);

            Mail::to($this->memberForm->email)
                ->locale($this->memberForm->locale)
                ->send(new InvitationMail($invitation, $this->memberForm->member));

            Flux::toast(
                text: __('members.backend.invitation.sent.msg'),
                heading: __('members.backend.invitation.sent.head'),
                variant: 'success',
            );
        } catch (\Throwable $e) {
            $this->logError('Einladung senden fehlgeschlagen', $e);
            Flux::toast(
                text: __('members.backend.invitation.failed.msg', ['error' => $this->userErrorMessage($e)]),
                heading: __('members.backend.invitation.failed.head'),
                variant: 'danger',
            );
        }
    }

    public function acceptApplication(): void
    {
        try {
            $this->checkPrivilege(Member::class);

            /** @var MemberApplication $application */
            $application = MemberApplication::query()->findOrFail($this->applicationId);

            $member = Member::createFromApplication(
                application: $application,
                gdprConsentAt: $application->gdpr_consent_at ?? Carbon::now(),
                newsletterConsentAt: $application->newsletter_consent_at,
                photoConsentAt: $application->photo_consent_at,
            );

            $member->entered_at = Carbon::now();
            $member->save();

            $application->delete();

            Flux::toast(
                text: __('members.notifications.accepted.success'),
                heading: __('members.apply.submission.success.head'),
                variant: 'success',
            );

            $this->redirect(route('backend.members.show', ['member' => $member]), true);
        } catch (\Throwable $e) {
            $this->handleError('Bewerbung annehmen fehlgeschlagen', $e);
        }
    }

    public function rejectApplication(): void
    {
        try {
            $this->checkPrivilege(Member::class);

            /** @var MemberApplication $application */
            $application = MemberApplication::query()->findOrFail($this->applicationId);

            $application->notify(new MemberRejectedNotification($application));

            $application->delete();

            Flux::toast(
                text: __('members.notifications.rejected.success'),
                heading: __('members.apply.submission.success.head'),
                variant: 'success',
            );

            $this->redirect(route('dashboard'), true);
        } catch (\Throwable $e) {
            $this->handleError('Bewerbung ablehnen fehlgeschlagen', $e);
        }
    }

    public function download(int $receipt_id): StreamedResponse
    {
        $receipt = Receipt::findOrFail($receipt_id);

        $filePath = "accounting/receipts/{$receipt->file_name}";

        if (! Storage::disk('local')->exists($filePath)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download($filePath);
    }

    public function bookItem(int $transaction_id): void
    {
        $this->authorize('book-item', Account::class);
        $this->dispatch('book-transaction', transactionId: $transaction_id);
        $this->transaction = Transaction::find($transaction_id);
        Flux::modal('book-transaction')->show();
    }

    public function editItem(int $transaction_id): void
    {
        $this->authorize('update', Account::class);
        $this->dispatch('edit-transaction', transactionId: $transaction_id);
        $this->transaction = Transaction::find($transaction_id);
        Flux::modal('add-new-payment')->show();
    }

    public function checkBirthDate(): void {}

    public function cancelMembership(): void
    {
        try {
            $this->authorize('cancel', $this->member);
            $this->cancelMyMembership = $this->member;
        } catch (\Throwable $e) {
            $this->handleError('Kündigungs-Modal öffnen fehlgeschlagen', $e);
        }
    }

    public function reactivateMembership(): void
    {
        try {
            $this->authorize('cancel', $this->member);
            if ($this->memberForm->reactivateMembership()) {
                Flux::toast(
                    text: __('members.backend.reactivate.success.msg'),
                    heading: __('members.backend.reactivate.success.head'),
                    variant: 'success',
                );
            }
        } catch (\Throwable $e) {
            $this->handleError('Mitglied reaktivieren fehlgeschlagen', $e);
        }
    }

    public function render(): View
    {
        return view('livewire.member.show.page')->title(__('members.show.heading'));
    }
}

<?php

namespace App\Livewire\Member;

use App\Livewire\Forms\Member\MemberForm;
use App\Livewire\Traits\HandlesErrors;
use App\Models\Membership\Member;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CancelMembership extends Component
{
    use HandlesErrors;

    public ?string $cancelDate = '';

    public ?string $cancelReason = '';  // optional, für interne Notiz

    public ?Member $selectedMember = null;

    public ?MemberForm $memberForm = null;

    public function mount(Member $member): void
    {
        $this->selectedMember = $member;
        $this->cancelDate = today()->toDateString();
        Flux::modal('cancel-membership')->show();
    }

    public function confirmCancelMembership(): void
    {
        try {
            $this->authorize('delete', $this->selectedMember);

            $this->validate([
                'cancelDate' => ['required', 'date'],
            ]);

            $this->memberForm->set($this->selectedMember);
            $this->memberForm->cancelMembership();

            Flux::modal('cancel-membership')->close();
            Flux::toast(
                text: __('members.backend.cancel.success.msg'),
                heading: __('members.backend.cancel.success.head'),
                variant: 'success',
            );
        } catch (\Throwable $e) {
            $this->handleError('Mitgliedschaft kündigen fehlgeschlagen', $e);
        }
    }

    public function deleteMembershipForSure(): void
    {
        try {
            $this->authorize('delete', Member::class);

            $msg = '';
            if ($this->memberForm->user_id !== null) {
                /** @var int $userId */
                $userId = $this->memberForm->user_id;
                /** @var \Illuminate\Contracts\Auth\Authenticatable&\App\Models\User $authUser */
                $authUser = Auth::user();

                if ($authUser->id !== $userId) {
                    $user = User::find($userId);
                    if ($user instanceof User) {
                        $msg = $user->delete()
                            ? ' '.__('members.backend.delete.user_deleted.msg')
                            : ' '.__('members.backend.delete.user_failed.msg', ['id' => $userId]);
                    }
                }
            }

            if ($this->memberForm->cancelMembership()) {
                Flux::toast(
                    text: __('members.backend.delete.success.msg').$msg,
                    heading: __('members.backend.delete.success.head'),
                    variant: 'success',
                );
            }
        } catch (\Throwable $e) {
            $this->handleError('Mitglied löschen fehlgeschlagen', $e);
        }
    }

    public function reactivateMembership(): void
    {
        try {
            $this->authorize('delete', Member::class);

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

    public function render()
    {
        return view('livewire.member.cancel-membership');
    }
}

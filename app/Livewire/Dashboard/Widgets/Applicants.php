<?php

declare(strict_types=1);

namespace App\Livewire\Dashboard\Widgets;

use App\Models\Membership\Member;
use App\Models\Membership\MemberApplication;
use App\Notifications\MemberAcceptedNotification;
use App\Notifications\MemberRejectedNotification;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Applicants extends Component
{
    use WithPagination;

    // ── List state ────────────────────────────────────────────────────────────

    /** @var array<int, string> */
    public array $selectedApplicants = [];

    /** @var array<int, string> */
    public array $applicantsOnPage = [];

    /** @var array<int, string> */
    public array $allApplicants = [];

    public ?MemberApplication $activeApplicant = null;

    public string $sortBy = 'applied_at';

    public string $sortDirection = 'desc';

    public string $search = '';

    public int $numApplicants = 0;

    // ── Detail / Accept modal ─────────────────────────────────────────────────

    public bool $showDetailModal = false;

    public ?int $activeApplicantId = null;

    // ── Reject modal ──────────────────────────────────────────────────────────

    public bool $showRejectModal = false;

    public string $rejectionReason = '';

    // ─────────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->numApplicants = MemberApplication::query()
            ->whereNotNull('verified_at')
            ->whereNull('accepted_at')
            ->whereNull('rejected_at')
            ->count();
    }

    // ── Sorting ───────────────────────────────────────────────────────────────

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    // ── Detail modal ──────────────────────────────────────────────────────────

    public function openDetailModal(int $applicantId): void
    {
        $this->activeApplicantId = $applicantId;
        $this->activeApplicant = MemberApplication::query()->find($applicantId);
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->activeApplicantId = null;
    }

    #[Computed]
    public function activeApplicant(): ?MemberApplication
    {
        if ($this->activeApplicantId === null) {
            return null;
        }

        return MemberApplication::find($this->activeApplicantId);
    }

    // ── Accept ────────────────────────────────────────────────────────────────

    public function acceptApplicant(): void
    {
        $application = $this->activeApplicant;

        if ($application === null) {
            return;
        }

        $member = Member::createFromApplication(
            application: $application,
            gdprConsentAt: $application->gdpr_consent_at ?? now(),
            newsletterConsentAt: $application->newsletter_consent_at,
            photoConsentAt: $application->photo_consent_at,
        );

        $application->update(['accepted_at' => now()]);

        $member->notify(new MemberAcceptedNotification($member));

        $this->closeDetailModal();
        $this->refreshCount();

        Flux::toast(
            text: __('members.widgets.applicants.confirm.accepted.text', ['name' => $member->name]),
            heading: __('members.widgets.applicants.confirm.accepted.title'),
            variant: 'success',
        );
    }

    // ── Reject ────────────────────────────────────────────────────────────────

    public function openRejectModal(): void
    {
        $this->rejectionReason = '';
        $this->showDetailModal = false;
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->rejectionReason = '';
    }

    public function rejectApplicant(): void
    {
        $this->validate([
            'rejectionReason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $application = $this->activeApplicant;

        if ($application === null) {
            return;
        }

        $application->update([
            'rejected_at' => now(),
            'rejection_reason' => $this->rejectionReason,
        ]);

        $application->notify(new MemberRejectedNotification($application));

        $this->closeRejectModal();
        $this->activeApplicantId = null;
        $this->refreshCount();

        Flux::toast(
            text: __('members.widgets.applicants.confirm.rejected.text'),
            heading: __('members.widgets.applicants.confirm.rejected.title'),
            variant: 'warning',
        );
    }

    // ── Bulk delete ───────────────────────────────────────────────────────────

    public function deleteSelectedApplicants(): void
    {
        if (count($this->selectedApplicants) === 0) {
            return;
        }

        if (! auth()->user()?->is_admin) {
            return;
        }

        MemberApplication::query()
            ->whereIn('id', $this->selectedApplicants)
            ->delete();

        Flux::toast(
            text: __('members.widgets.applicants.confirm.deletion.text'),
            heading: __('members.widgets.applicants.confirm.deletion.title'),
            variant: 'success',
        );

        $this->selectedApplicants = [];
        $this->refreshCount();
    }

    // ── Query ─────────────────────────────────────────────────────────────────

    #[Computed]
    public function applicants(): LengthAwarePaginator
    {
        $query = MemberApplication::query()
            ->whereNotNull('verified_at')
            ->whereNull('accepted_at')
            ->whereNull('rejected_at')
            ->when(
                $this->search !== '',
                fn ($q) => $q->where('name', 'like', '%'.$this->search.'%')
            )
            ->orderBy($this->sortBy, $this->sortDirection);

        $paginated = $query->paginate(5);

        $this->allApplicants = MemberApplication::query()
            ->whereNotNull('verified_at')
            ->whereNull('accepted_at')
            ->whereNull('rejected_at')
            ->pluck('id')
            ->map(fn ($id): string => (string) $id)
            ->toArray();

        $this->applicantsOnPage = $paginated
            ->map(fn ($application): string => (string) $application->id)
            ->toArray();

        return $paginated;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function refreshCount(): void
    {
        $this->numApplicants = MemberApplication::query()
            ->whereNotNull('verified_at')
            ->whereNull('accepted_at')
            ->whereNull('rejected_at')
            ->count();

        unset($this->applicants);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.dashboard.widgets.applicants');
    }
}

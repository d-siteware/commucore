<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Project;

use App\Enums\FundingStatus;
use App\Helpers\MoneyHelper;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Funding\Funding;
use App\Models\Project\Project;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

final class LinkFundingForm extends Component
{
    use HasPrivileges;

    public Project $project;

    public ?int $funding_id = null;

    public string $allocated_amount = '';

    public bool $isEditing = false;

    /** @var Collection<int, Funding> */
    public Collection $availableFundings;

    public function mount(Project $project): void
    {
        $this->project = $project;
        $this->loadAvailableFundings();
    }

    #[On('start-edit-funding')]
    public function startEdit(int $fundingId, int $allocatedAmount): void
    {
        $this->isEditing = true;
        $this->funding_id = $fundingId;
        $this->allocated_amount = MoneyHelper::centsToFormInput($allocatedAmount);
    }

    public function loadAvailableFundings(): void
    {
        $linkedIds = $this->project->fundings()->pluck('fundings.id');

        // Nur Fundings die verknüpft werden dürfen:
        // - Nicht bereits verknüpft
        // - Status nicht Completed oder Rejected
        // - Noch Budget vorhanden (remainingAmount > 0)
        $this->availableFundings = Funding::query()
            ->whereNotIn('id', $linkedIds)
            ->whereNotIn('status', [FundingStatus::Completed->value, FundingStatus::Rejected->value])
            ->get(['id', 'title', 'funder', 'approved_amount', 'status'])
            ->filter(fn (Funding $f) => $f->remainingAmount() > 0)
            ->values();
    }

    public function attach(): void
    {
        $this->checkPrivilege(Project::class);

        $this->validate([
            'funding_id' => ['required', 'integer', 'exists:fundings,id'],
            'allocated_amount' => ['required', 'string'],
        ]);

        if ($this->project->fundings()->where('fundings.id', $this->funding_id)->exists()) {
            Flux::toast(text: __('projects.link_funding.error.already_linked'), variant: 'danger');

            return;
        }

        $funding = Funding::findOrFail($this->funding_id);
        $cents = MoneyHelper::toCents($this->allocated_amount);
        $remaining = $funding->remainingAmount();

        if ($cents === null || $cents <= 0) {
            Flux::toast(text: __('projects.link_funding.error.invalid_amount'), variant: 'danger');

            return;
        }

        if ($cents > $remaining) {
            Flux::toast(
                text: __('projects.link_funding.error.exceeds_remaining', [
                    'remaining' => MoneyHelper::formatCents($remaining),
                ]),
                variant: 'danger',
            );

            return;
        }

        $this->project->fundings()->attach($this->funding_id, [
            'allocated_amount' => $cents,
        ]);

        Flux::toast(text: __('projects.link_funding.success.attached'), variant: 'success');

        $this->reset(['funding_id', 'allocated_amount', 'isEditing']);
        $this->loadAvailableFundings();
        $this->dispatch('funding-linked');
        Flux::modal('link-funding-modal')->close();
    }

    public function updatePivot(): void
    {
        $this->checkPrivilege(Project::class);

        $this->validate([
            'funding_id' => ['required', 'integer', 'exists:fundings,id'],
            'allocated_amount' => ['required', 'string'],
        ]);

        $cents = MoneyHelper::toCents($this->allocated_amount);

        if ($cents === null || $cents <= 0) {
            Flux::toast(text: __('projects.link_funding.error.invalid_amount'), variant: 'danger');

            return;
        }

        $funding = Funding::findOrFail($this->funding_id);

        // Aktuellen Pivot-Betrag addieren damit unveränderte Speicherung nicht blockiert wird
        $current = (int) ($this->project->fundings()
            ->where('fundings.id', $this->funding_id)
            ->first()?->pivot->allocated_amount ?? 0);
        $remaining = $funding->remainingAmount() + $current;

        if ($cents > $remaining) {
            Flux::toast(
                text: __('projects.link_funding.error.exceeds_remaining', [
                    'remaining' => MoneyHelper::formatCents($remaining),
                ]),
                variant: 'danger',
            );

            return;
        }

        $this->project->fundings()->updateExistingPivot($this->funding_id, [
            'allocated_amount' => $cents,
        ]);

        Flux::toast(text: __('projects.link_funding.success.updated'), variant: 'success');

        $this->reset(['funding_id', 'allocated_amount', 'isEditing']);
        $this->dispatch('funding-linked');
        Flux::modal('link-funding-modal')->close();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.activity.project.link-funding-form');
    }
}

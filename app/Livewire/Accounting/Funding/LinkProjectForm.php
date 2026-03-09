<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Funding;

use App\Enums\FundingStatus;
use App\Helpers\MoneyHelper;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Funding\Funding;
use App\Models\Project\Project;
use Flux\Flux;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

final class LinkProjectForm extends Component
{
    use HasPrivileges;

    public Funding $funding;

    public ?int $project_id = null;

    public string $allocated_amount = '';

    public bool $isEditing = false;

    /** @var Collection<int, Project> */
    public Collection $availableProjects;

    public function mount(Funding $funding): void
    {
        $this->funding = $funding;
        $this->loadAvailableProjects();
    }

    #[On('start-edit-project')]
    public function startEdit(int $projectId, int $allocatedAmount): void
    {
        $this->isEditing = true;
        $this->project_id = $projectId;
        $this->allocated_amount = MoneyHelper::centsToFormInput($allocatedAmount);
    }

    public function loadAvailableProjects(): void
    {
        $linkedIds = $this->funding->projects()->pluck('projects.id');

        $this->availableProjects = Project::query()
            ->whereNotIn('id', $linkedIds)
            ->orderBy('title')
            ->get(['id', 'title', 'status', 'start_date', 'end_date']);
    }

    public function attach(): void
    {
        $this->checkPrivilege(Funding::class);

        $this->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'allocated_amount' => ['required', 'string'],
        ]);

        if ($this->funding->projects()->where('projects.id', $this->project_id)->exists()) {
            Flux::toast(text: __('fundings.link_project.error.already_linked'), variant: 'danger');

            return;
        }

        $cents = MoneyHelper::toCents($this->allocated_amount);
        $remaining = $this->funding->remainingAmount();

        if ($cents === null || $cents <= 0) {
            Flux::toast(text: __('fundings.link_project.error.invalid_amount'), variant: 'danger');

            return;
        }

        if ($cents > $remaining) {
            Flux::toast(
                text: __('fundings.link_project.error.exceeds_remaining', [
                    'remaining' => MoneyHelper::formatCents($remaining),
                ]),
                variant: 'danger',
            );

            return;
        }

        $this->funding->projects()->attach($this->project_id, [
            'allocated_amount' => $cents,
        ]);

        Flux::toast(text: __('fundings.link_project.success.attached'), variant: 'success');

        $this->reset(['project_id', 'allocated_amount', 'isEditing']);
        $this->loadAvailableProjects();
        $this->dispatch('project-linked');
        Flux::modal('link-project-modal')->close();
    }

    public function updatePivot(): void
    {
        $this->checkPrivilege(Funding::class);

        $this->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'allocated_amount' => ['required', 'string'],
        ]);

        $cents = MoneyHelper::toCents($this->allocated_amount);

        if ($cents === null || $cents <= 0) {
            Flux::toast(text: __('fundings.link_project.error.invalid_amount'), variant: 'danger');

            return;
        }

        // Aktuellen Pivot-Betrag addieren, damit eine unveränderte Speicherung nicht blockiert wird
        $current = (int) ($this->funding->projects()
            ->where('projects.id', $this->project_id)
            ->first()?->pivot->allocated_amount ?? 0);
        $remaining = $this->funding->remainingAmount() + $current;

        if ($cents > $remaining) {
            Flux::toast(
                text: __('fundings.link_project.error.exceeds_remaining', [
                    'remaining' => MoneyHelper::formatCents($remaining),
                ]),
                variant: 'danger',
            );

            return;
        }

        $this->funding->projects()->updateExistingPivot($this->project_id, [
            'allocated_amount' => $cents,
        ]);

        Flux::toast(text: __('fundings.link_project.success.updated'), variant: 'success');

        $this->reset(['project_id', 'allocated_amount', 'isEditing']);
        $this->dispatch('project-linked');
        Flux::modal('link-project-modal')->close();
    }

    public function hasRemainingBudget(): bool
    {
        return $this->funding->remainingAmount() > 0
            && ! in_array($this->funding->status, [FundingStatus::Completed, FundingStatus::Rejected], true);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.accounting.funding.link-project-form');
    }
}

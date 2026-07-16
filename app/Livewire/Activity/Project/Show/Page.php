<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Project\Show;

use App\Livewire\Forms\Activity\ProjectForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\PersistsTabs;
use App\Livewire\Traits\Sortable;
use App\Models\Blog\Post;
use App\Models\Funding\Funding;
use App\Models\Project\Project;
use App\Models\Project\ProjectTransaction;
use App\Services\ProjectFundingReportService;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Page extends Component
{
    use HandlesErrors;
    use HasPrivileges;
    use PersistsTabs;
    use Sortable;
    use WithPagination;

    public ProjectForm $form;

    public Project $project;

    public ?string $defaultTab = 'project-show-details';

    public string $selectedTab = 'project-show-details';

    protected $listeners = [
        'transaction-attached' => '$refresh',
        'funding-linked' => '$refresh',
    ];

    public function mount(Project $project): void
    {
        $this->project = $project;
        $this->selectedTab = $this->getSelectedTab();
        $this->form->setProject($project);
    }

    // =========================================================================
    // Computed
    // =========================================================================

    #[Computed]
    public function transactions(): LengthAwarePaginator
    {
        return ProjectTransaction::query()
            ->with('transaction')
            ->where('project_id', $this->project->id)
            ->tap(fn ($q) => $this->sortBy
                ? $q->orderBy($this->sortBy, $this->sortDirection)
                : $q
            )
            ->paginate(10);
    }

    /** @return Collection<int, Funding> */
    #[Computed]
    public function fundings(): Collection
    {
        /** @var Collection<int, Funding> */
        return $this->project->fundings()->withPivot('allocated_amount')->get();
    }

    #[Computed]
    public function posts(): LengthAwarePaginator
    {
        return Post::query()
            ->forProject($this->project->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    #[Computed]
    public function totalIncome(): int
    {
        return $this->project->totalIncome();
    }

    #[Computed]
    public function totalExpense(): int
    {
        return $this->project->totalExpense();
    }

    #[Computed]
    public function fundingAllocated(): int
    {
        return $this->project->totalFundingAllocated();
    }

    // =========================================================================
    // Actions
    // =========================================================================

    public function updateProject(): void
    {
        try {
            $this->checkPrivilege(Project::class);
            $this->form->update($this->project);

            Flux::toast(
                text: __('projects.show.toast.updated'),
                variant: 'success',
            );
        } catch (\Throwable $e) {
            $this->handleError('Projekt aktualisieren fehlgeschlagen', $e);
        }
    }

    public function deleteProject(): void
    {
        try {
            $this->checkPrivilege(Project::class);
            $this->project->delete();

            $this->redirect(route('project.index'), navigate: true);
        } catch (\Throwable $e) {
            $this->handleError('Projekt löschen fehlgeschlagen', $e);
        }
    }

    /**
     * Öffnet das Flyout-Modal zum Bearbeiten einer bestehenden Pivot-Verknüpfung.
     * Dispatcht ein Event an die child-Komponente LinkFundingForm.
     */
    public function openEditFunding(int $fundingId, int $allocatedAmount): void
    {
        $this->dispatch('start-edit-funding',
            fundingId: $fundingId,
            allocatedAmount: $allocatedAmount,
        );

        Flux::modal('link-funding-modal')->show();
    }

    /**
     * Löst die Verknüpfung zwischen Projekt und Förderung direkt in der Show-Page.
     * unset($this->fundings) invalidiert den Computed Cache.
     */
    public function detachFunding(int $fundingId): void
    {
        try {
            $this->checkPrivilege(Project::class);

            $this->project->fundings()->detach($fundingId);

            Flux::toast(
                text: __('projects.link_funding.success.detached'),
                variant: 'warning',
            );

            unset($this->fundings);
        } catch (\Throwable $e) {
            $this->handleError('Förderung trennen fehlgeschlagen', $e);
        }
    }

    public function createExecutiveReport(): void
    {
        $this->createReport('summary');
    }

    public function createDetailedReport(): void
    {
        $this->createReport('detailed');
    }

    private function createReport(string $variant): void
    {
        try {
            $this->checkPrivilege(Project::class);

            app(ProjectFundingReportService::class)->createProjectReport($this->project, $variant);

            Flux::toast(
                text: __('projects.reports.toast.created'),
                variant: 'success',
            );

            $this->selectedTab = 'project-show-documents';
        } catch (\Throwable $e) {
            $this->handleError('Projektbericht erstellen fehlgeschlagen', $e);
        }
    }

    public function render(): View
    {
        return view('livewire.activity.project.show.page')
            ->title(__('projects.show.title').' '.$this->project->title);
    }
}

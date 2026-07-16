<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Funding\Show;

use App\Livewire\Forms\Accounting\FundingForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\PersistsTabs;
use App\Livewire\Traits\Sortable;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingTransaction;
use App\Models\Project\Project;
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

    public FundingForm $form;

    public Funding $funding;

    public ?string $defaultTab = 'funding-show-details';

    public ?string $selectedTab = 'funding-show-details';

    protected $listeners = [
        'transaction-attached' => '$refresh',
        'project-linked' => '$refresh',
    ];

    public function mount(Funding $funding): void
    {
        $this->funding = $funding;
        $this->selectedTab = $this->getSelectedTab();
        $this->form->setFunding($funding);
    }

    // =========================================================================
    // Computed
    // =========================================================================

    #[Computed]
    public function transactions(): LengthAwarePaginator
    {
        return FundingTransaction::query()
            ->with('transaction')
            ->where('funding_id', $this->funding->id)
            ->tap(fn ($q) => $this->sortBy
                ? $q->orderBy($this->sortBy, $this->sortDirection)
                : $q
            )
            ->paginate(10);
    }

    /** @return Collection<int, Project> */
    #[Computed]
    public function projects(): Collection
    {
        /** @var Collection<int, Project> */
        return $this->funding->projects()->withPivot('allocated_amount')->get();
    }

    #[Computed]
    public function totalAllocated(): int
    {
        /** @var Collection<int, Project> $projects */
        $projects = $this->funding->projects()->withPivot('allocated_amount')->get();

        return $projects->sum(fn ($p) => (int) ($p->pivot->allocated_amount ?? 0));
    }

    /** Total received (all incoming FundingTransactions) in Cent */
    #[Computed]
    public function totalReceived(): int
    {
        return $this->funding->totalReceived();
    }

    #[Computed]
    public function approvedAmount(): int
    {
        return $this->funding->approved_amount ?? 0;
    }

    // =========================================================================
    // Actions
    // =========================================================================

    public function updateFunding(): void
    {
        try {
            $this->checkPrivilege(Funding::class);
            $this->form->update($this->funding);

            Flux::toast(
                text: __('fundings.show.toast.updated'),
                variant: 'success',
            );
        } catch (\Throwable $e) {
            $this->handleError('Förderung aktualisieren fehlgeschlagen', $e);
        }
    }

    public function deleteFunding(): void
    {
        try {
            $this->checkPrivilege(Funding::class);
            $this->funding->delete();

            $this->redirect(route('funding.index'), navigate: true);
        } catch (\Throwable $e) {
            $this->handleError('Förderung löschen fehlgeschlagen', $e);
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
            $this->checkPrivilege(Funding::class);

            app(ProjectFundingReportService::class)->createFundingReport($this->funding, $variant);

            Flux::toast(
                text: __('fundings.reports.toast.created'),
                variant: 'success',
            );

            $this->selectedTab = 'funding-show-documents';
        } catch (\Throwable $e) {
            $this->handleError('Förderbericht erstellen fehlgeschlagen', $e);
        }
    }

    public function render(): View
    {
        return view('livewire.accounting.funding.show.page')
            ->title(__('fundings.show.title').' '.$this->funding->title);
    }
}

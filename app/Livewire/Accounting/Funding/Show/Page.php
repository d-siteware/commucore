<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Funding\Show;

use App\Livewire\Forms\Accounting\FundingForm;
use App\Livewire\Forms\Accounting\FundingPositionForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\PersistsTabs;
use App\Livewire\Traits\Sortable;
use App\Models\Funding\Funding;
use App\Models\Funding\FundingPosition;
use App\Models\Funding\FundingPositionCategory;
use App\Models\Funding\FundingTransaction;
use App\Models\Membership\Member;
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

    public FundingPositionForm $positionForm;

    public string $newCategoryName = '';

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
            ->with(['transaction', 'fundingPosition'])
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

    /** @return Collection<int, FundingPosition> */
    #[Computed]
    public function positions(): Collection
    {
        /** @var Collection<int, FundingPosition> */
        return $this->funding->fundingPositions()
            ->with(['category', 'responsible'])
            ->orderBy('title')
            ->get();
    }

    #[Computed]
    public function positionsBudgetSum(): int
    {
        return (int) $this->funding->fundingPositions()->sum('budget');
    }

    /**
     * Weicher Konsistenzhinweis: Summe der Positions-Budgets übersteigt den
     * bewilligten Gesamtbetrag (bewusst kein harter Block).
     */
    #[Computed]
    public function positionsBudgetExceeded(): bool
    {
        $approved = $this->funding->approved_amount;

        return $approved !== null && $approved > 0 && $this->positionsBudgetSum() > $approved;
    }

    /** @return Collection<int, FundingPositionCategory> */
    #[Computed]
    public function positionCategories(): Collection
    {
        /** @var Collection<int, FundingPositionCategory> */
        return FundingPositionCategory::query()
            ->withCount('fundingPositions')
            ->orderBy('sort')
            ->orderBy('name')
            ->get();
    }

    /** @return Collection<int, Member> */
    #[Computed]
    public function members(): Collection
    {
        /** @var Collection<int, Member> */
        return Member::query()
            ->orderBy('name')
            ->orderBy('first_name')
            ->get(['id', 'name', 'first_name']);
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

    // =========================================================================
    // Positionen (Plan/Ist je Förderposition)
    // =========================================================================

    public function editPosition(?int $positionId = null): void
    {
        $this->positionForm->reset();

        if ($positionId !== null) {
            /** @var FundingPosition $position */
            $position = $this->funding->fundingPositions()->findOrFail($positionId);
            $this->positionForm->setPosition($position);
        }

        Flux::modal('funding-position-modal')->show();
    }

    public function savePosition(): void
    {
        try {
            $this->checkPrivilege(Funding::class);

            if ($this->positionForm->id !== null) {
                /** @var FundingPosition $position */
                $position = $this->funding->fundingPositions()->findOrFail($this->positionForm->id);
                $this->positionForm->update($position);
            } else {
                $this->positionForm->store($this->funding);
            }

            Flux::toast(
                text: __('fundings.positions.toast.saved'),
                variant: 'success',
            );

            Flux::modal('funding-position-modal')->close();
            $this->positionForm->reset();
            unset($this->positions, $this->positionsBudgetSum, $this->positionsBudgetExceeded);
        } catch (\Throwable $e) {
            $this->handleError('Position speichern fehlgeschlagen', $e);
        }
    }

    public function deletePosition(int $positionId): void
    {
        try {
            $this->checkPrivilege(Funding::class);

            $this->funding->fundingPositions()->findOrFail($positionId)->delete();

            Flux::toast(
                text: __('fundings.positions.toast.deleted'),
                variant: 'success',
            );

            unset($this->positions, $this->positionsBudgetSum, $this->positionsBudgetExceeded);
        } catch (\Throwable $e) {
            $this->handleError('Position löschen fehlgeschlagen', $e);
        }
    }

    // =========================================================================
    // Kategorien (System read-only, Custom im "custom:"-Namensraum)
    // =========================================================================

    public function addCategory(): void
    {
        if (! auth()->user()?->is_admin) {
            return;
        }

        $validated = $this->validate([
            'newCategoryName' => ['required', 'string', 'max:255'],
        ]);

        $name = trim($validated['newCategoryName']);
        $slug = FundingPositionCategory::CUSTOM_SLUG_PREFIX.\Illuminate\Support\Str::slug($name);

        if ($slug === FundingPositionCategory::CUSTOM_SLUG_PREFIX
            || FundingPositionCategory::where('slug', $slug)->exists()) {
            Flux::toast(
                text: __('fundings.positions.categories.error.duplicate'),
                variant: 'danger',
            );

            return;
        }

        FundingPositionCategory::create([
            'slug' => $slug,
            'name' => $name,
            'is_system' => false,
            'source' => 'custom',
            'sort' => ((int) FundingPositionCategory::max('sort')) + 10,
        ]);

        $this->reset('newCategoryName');

        Flux::toast(
            text: __('fundings.positions.categories.toast.created'),
            variant: 'success',
        );

        unset($this->positionCategories);
    }

    public function deleteCategory(int $categoryId): void
    {
        if (! auth()->user()?->is_admin) {
            return;
        }

        try {
            FundingPositionCategory::findOrFail($categoryId)->delete();

            Flux::toast(
                text: __('fundings.positions.categories.toast.deleted'),
                variant: 'success',
            );

            unset($this->positionCategories);
        } catch (\Throwable $e) {
            $this->handleError('Kategorie löschen fehlgeschlagen', $e);
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

    public function createStatusReport(): void
    {
        $this->createReport('statusbericht');
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

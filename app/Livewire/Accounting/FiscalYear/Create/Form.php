<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\FiscalYear\Create;

use App\Models\Accounting\FiscalYear;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Component;

final class Form extends Component
{
    public int $year;

    public string $opened_at = '';

    public bool $showWarning = false;

    public string $warningMessage = '';

    public bool $acceptWarning = true;

    public bool $existingFY = false;

    public function mount(): void
    {
        // Default: Nächstes Jahr
        $latestYear = FiscalYear::max('year');
        $this->year = $latestYear ? $latestYear + 1 : Carbon::now()->year;
        $this->opened_at = Carbon::now()->format('Y-m-d');
    }

    public function updatedYear(): void
    {
        $this->checkForWarnings();
    }

    public function updatedOpenedAt(): void
    {
        $this->checkForWarnings();
    }

    private function checkForWarnings(): void
    {
        if (! $this->year || ! $this->opened_at) {
            return;
        }

        $this->existingFY = false;

        $openedDate = Carbon::parse($this->opened_at);
        $currentYear = Carbon::now()->year;

        // Warnung 0: FY ist bereits in Verwendung
        if (FiscalYear::where('year', $this->year)->exists()) {
            $this->showWarning = true;
            $this->acceptWarning = false;
            $this->existingFY = true;
            $this->warningMessage = __('fiscal_year.validation.warning_existing_year', [
                'year' => $this->year,
            ]);

            return;
        }

        // Warnung 1: FY liegt weit in der Vergangenheit (mehr als 2 Jahre)
        if ($this->year < ($currentYear - 2)) {
            $this->showWarning = true;
            $this->acceptWarning = false;
            $this->warningMessage = __('fiscal_year.validation.warning_past_year', [
                'year' => $this->year,
                'years_ago' => $currentYear - $this->year,
            ]);

            return;
        }

        // Warnung 2: FY liegt in der Zukunft (mehr als 1 Jahr voraus)
        if ($this->year > ($currentYear + 1)) {
            $this->showWarning = true;
            $this->acceptWarning = false;
            $this->warningMessage = __('fiscal_year.validation.warning_future_year', [
                'year' => $this->year,
            ]);

            return;
        }

        // Warnung 3: opened_at liegt nicht im Jahr des FY
        if ($openedDate->year != $this->year) {
            $this->showWarning = true;
            $this->acceptWarning = false;
            $this->warningMessage = __('fiscal_year.validation.warning_date_mismatch', [
                'year' => $this->year,
                'date_year' => $openedDate->year,
            ]);

            return;
        }

        $this->acceptWarning = true;
        $this->showWarning = false;
        $this->warningMessage = '';
    }

    public function save(): void
    {
        if ($this->existingFY) {
            Flux::toast(
                text: __('fiscal_year.validation.warning_existing_year', ['year' => $this->year]),
                heading: __('fiscal_year.validation.warning_title'),
                variant: 'danger'
            );

            return;
        }
        $this->authorize('create', FiscalYear::class);

        $this->validate([
            'year' => [
                'required',
                'integer',
                'min:2000',
                'max:2100',
                'unique:fiscal_years,year',
            ],
            'opened_at' => 'required|date',
        ], [
            'year.required' => __('fiscal_year.validation.year_required'),
            'year.unique' => __('fiscal_year.validation.year_exists'),
            'year.min' => __('fiscal_year.validation.year_min'),
            'year.max' => __('fiscal_year.validation.year_max'),
            'opened_at.required' => __('fiscal_year.validation.opened_at_required'),
            'opened_at.date' => __('fiscal_year.validation.opened_at_date'),
            'opened_at.before_or_equal' => __('fiscal_year.validation.opened_at_future'),
        ]);

        if ($this->acceptWarning) {
            try {
                FiscalYear::create([
                    'year' => $this->year,
                    'opened_at' => Carbon::parse($this->opened_at),
                    'opened_by' => auth()->id(),
                ]);

                $this->dispatch('fiscal-year-created');

                Flux::toast(__('fiscal_year.create.created_toast'));
            } catch (\Exception $e) {
                $this->addError('save', __('fiscal_year.validation.creation_failed'));
            }
        }
    }

    public function cancel(): void
    {
        $this->reset();
        Flux::modal('make-new-fiscal-year-modal')->hide();
    }

    public function getSuccessMessageProperty(): string
    {
        return __('fiscal_year.created_successfully', ['year' => $this->year]);
    }

    public function render(): View
    {
        return view('livewire.accounting.fiscal-year.create.page');
    }
}

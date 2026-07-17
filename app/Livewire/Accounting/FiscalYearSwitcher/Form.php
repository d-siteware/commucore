<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\FiscalYearSwitcher;

use App\Models\Accounting\FiscalYear;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Component;

final class Form extends Component
{
    /** @var Collection<int, FiscalYear> */
    public $fiscalYears;

    public ?int $currentFiscalYearId = null;

    public function mount(): void
    {
        $this->fiscalYears = FiscalYear::query()
            ->select('id', 'year', 'closed_at')
            ->orderByDesc('year')
            ->get();

        $sessionId = session('fiscalYearId');
        $this->currentFiscalYearId = $sessionId !== null
            ? (int) $sessionId
            : (FiscalYear::getActive()->id
                ?? FiscalYear::getOrCreate(now(config('commucore.accounting_timezone'))->year)->id);
    }

    public function setFY(int $fiscalYearId): void
    {
        FiscalYear::findOrFail($fiscalYearId);
        Session::put('fiscalYearId', $fiscalYearId);
        $this->redirect(request()->header('Referer') ?? '/dashboard');
    }

    public function render(): View
    {
        return view('livewire.accounting.fiscal-year-switcher.form');
    }
}

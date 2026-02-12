<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\FiscalYearSwitcher;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

final class Form extends Component
{
    public array $fiscalYears = [];

    public int $currentFiscalYear;

    public function mount(): void
    {
        $this->currentFiscalYear = session('financialYear') ?? Carbon::now()->format('Y');
        $this->fiscalYears = DB::table('transactions')
            ->selectRaw('DISTINCT strftime("%Y", date) as year')
            ->orderBy('year', 'asc')
            ->pluck('year')
            ->all();
    }

    public function setFY(string $fy): void
    {
        Session::put('financialYear', $fy);
        $this->redirect(request()->header('Referer') ?? '/dashboard');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.accounting.fiscal-year-switcher.form');
    }
}

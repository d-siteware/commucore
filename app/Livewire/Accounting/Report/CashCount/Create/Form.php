<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Report\CashCount\Create;

use App\Livewire\Accounting\Index\Page;
use App\Livewire\Forms\Accounting\CashCountForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Accounting\Account;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Component;

final class Form extends Component
{
    use HandlesErrors;
    use HasPrivileges;

    public CashCountForm $form;

    public $accountId;

    public function mount(int $accountId): void
    {
        $this->form->init();
        $this->form->account_id = $accountId;
        $this->form->user_id = auth()->id();
        $this->form->counted_at = Carbon::today('Europe/Berlin')->format('Y-m-d');
    }

    public function store(): void
    {
        try {
            $this->checkPrivilege(Account::class);
            $this->form->create();
            Flux::toast(__('cash_count.created'));
            $this->redirect(Page::class);
        } catch (\Throwable $e) {
            $this->handleError('Kassensturz speichern fehlgeschlagen', $e);
        }
    }

    public function render(): View
    {
        return view('livewire.accounting.report.cash-count.create.form');
    }
}

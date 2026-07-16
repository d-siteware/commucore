<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Account\Index;

use App\Enums\AccountType;
use App\Enums\TransactionStatus;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\PersistsTabs;
use App\Livewire\Traits\Sortable;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\CashCount;
use App\Models\Accounting\Transaction;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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

    public Account $account;

    public $selectedAccount;

    public bool $is_cash_account;

    public $defaultTab = 'account-index-reports';

    public string $selectedTab;

    public bool $account_is_set = false;

    protected $listeners = [
        'account-updated' => '$refresh',
        'account-report-generated' => 'accountReportCreated',
    ];

    #[Computed]
    public function accounts(): LengthAwarePaginator
    {
        return Account::query()
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(10);
    }

    #[Computed]
    public function transactions(): LengthAwarePaginator
    {
        return Transaction::query()
            ->where('account_id', $this->account->id)
            ->where('status', '=', TransactionStatus::booked->value)
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(10);
    }

    #[Computed]
    public function reports(): LengthAwarePaginator
    {
        return AccountReport::query()
            ->where('account_id', $this->account->id)
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(10);
    }

    #[Computed]
    public function cashCounts(): LengthAwarePaginator
    {
        return CashCount::query()
            ->where('account_id', $this->account->id)
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(10);
    }

    public function updatedSelectedAccount(): void
    {
        $this->account = Account::query()->findOrFail($this->selectedAccount);
        $this->account_is_set = true;
        $this->is_cash_account = $this->account->type === AccountType::cash;
    }

    public function editAccount(): void
    {
        $this->checkPrivilege(Account::class);
        $this->account = Account::query()->findOrFail($this->selectedAccount);
        $this->account_is_set = true;
        $this->is_cash_account = $this->account->type === AccountType::cash;
    }

    public function createCashCountReport(): void
    {
        $this->checkPrivilege(Account::class);
        Flux::modal('create-cash-count')->show();
    }

    public function createReport(): void
    {
        $this->checkPrivilege(Account::class);
        Flux::modal('create-monthly-report')->show();
    }

    public function accountReportCreated(): void
    {
        Flux::modal('create-monthly-report')->close();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.accounting.account.index.page')->title(__('account.index.title'));
    }
}

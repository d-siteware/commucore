<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use App\Actions\Report\CreateAccountReport;
use App\Actions\Report\UpdateAccountReport;
use App\Enums\ReportStatus;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountReport;
use Illuminate\Validation\Rule;
use Livewire\Form;

final class AccountReportForm extends Form
{
    public $id;

    public $account_id;

    public $starting_amount;

    public $end_amount;

    public $created_by;

    public $period_start;

    public $period_end;

    public $total_income;

    public $total_expenditure;

    public $status;

    public $audited_at;

    public $auditor_id;

    public $notes;

    public function set(AccountReport $accountReport): void
    {
        $this->id = $accountReport->id;
        $this->account_id = $accountReport->account_id;
        $this->starting_amount = Account::formatedAmount($accountReport->starting_amount);
        $this->end_amount = Account::formatedAmount($accountReport->end_amount);
        $this->created_by = $accountReport->created_by;
        $this->period_start = $accountReport->period_start;
        $this->period_end = $accountReport->period_end;
        $this->total_income = Account::formatedAmount($accountReport->total_income);
        $this->total_expenditure = Account::formatedAmount($accountReport->total_expenditure);
        $this->status = $accountReport->status;
        $this->notes = $accountReport->notes;
    }

    public function create(): AccountReport
    {
        $this->validate();

        return CreateAccountReport::handle($this);
    }

    public function update(AccountReportForm $data): bool
    {

        $this->validate();

        return UpdateAccountReport::handle($this);

    }

    protected function rules(): array
    {
        return [
            'account_id' => ['required'],
            'starting_amount' => ['required'],
            'end_amount' => ['required'],
            'created_by' => ['required'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after:period_start'],
            'total_income' => ['required'],
            'total_expenditure' => ['required'],
            'status' => ['required', Rule::enum(ReportStatus::class)],
            'notes' => 'nullable|string',
        ];
    }

    protected function messages(): array
    {
        return [];
    }
}

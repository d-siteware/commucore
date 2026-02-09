<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Report\Audit;

use App\Enums\TransactionStatus;
use App\Livewire\Forms\Accounting\AccountReportAuditForm;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\AccountReportAudit;
use App\Models\Accounting\Transaction;
use Carbon\Carbon;
use Livewire\Component;

final class Page extends Component
{
    use HasPrivileges;

    public int $accountReportAuditId;

    public $transactions;

    public AccountReportAudit $audit;

    public AccountReportAuditForm $form;

    public AccountReport $report;

    public function mount(int $accountReportAuditId): void
    {

        $this->form->set($accountReportAuditId);

        $this->accountReportAuditId = $accountReportAuditId;
        $this->audit = AccountReportAudit::query()->findOrFail($this->accountReportAuditId);

        $this->report = AccountReport::query()->find($this->audit->account_report_id);

        $this->transactions = Transaction::query()->where('account_id', '=', $this->report->account->id)
            ->where('status', TransactionStatus::booked->value)
            ->whereBetween('date', [$this->report->period_start, $this->report->period_end])
            ->orderBy('date')
            ->get();

    }

    public function approveAuditReport(): void
    {
        $this->form->is_approved = true;
        $this->form->approved_at = Carbon::now('Europe/Berlin');
        $this->form->update();

        AccountReport::setReportStatus($this->accountReportAuditId);

        $this->redirect(\App\Livewire\Accounting\Report\Index\Page::class);

    }

    public function rejectAuditReport(): void
    {
        $this->validate([
            'form.reason' => 'required',
        ], [
            'form.reason.required' => 'Bitte eine Begründung angeben!',
        ]);
        $this->form->is_approved = false;
        $this->form->approved_at = Carbon::now('Europe/Berlin');
        $this->form->update();

        AccountReport::setReportStatus($this->accountReportAuditId);

        $this->redirect(\App\Livewire\Accounting\Report\Index\Page::class);

    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.accounting.report.audit.page');
    }
}

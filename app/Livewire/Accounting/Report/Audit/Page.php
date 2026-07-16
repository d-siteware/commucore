<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Report\Audit;

use App\Livewire\Forms\Accounting\AccountReportAuditForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\AccountReportAudit;
use App\Models\Accounting\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Component;

final class Page extends Component
{
    use HandlesErrors;
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
            ->financialReportable()
            ->whereBetween('date', [$this->report->period_start, $this->report->period_end])
            ->orderBy('date')
            ->get();

    }

    public function approveAuditReport(): void
    {
        try {
            $this->checkPrivilege(AccountReport::class);
            $this->form->is_approved = true;
            $this->form->approved_at = Carbon::now('Europe/Berlin');
            $this->form->update();

            AccountReport::setReportStatus($this->accountReportAuditId);

            $this->redirect(\App\Livewire\Accounting\Report\Index\Page::class);
        } catch (\Throwable $e) {
            $this->handleError('Prüfbericht genehmigen fehlgeschlagen', $e);
        }
    }

    public function rejectAuditReport(): void
    {
        try {
            $this->checkPrivilege(AccountReport::class);
            $this->validate([
                'form.reason' => 'required',
            ], [
                'form.reason.required' => __('account_report_audit.reason_required'),
            ]);
            $this->form->is_approved = false;
            $this->form->approved_at = Carbon::now('Europe/Berlin');
            $this->form->update();

            AccountReport::setReportStatus($this->accountReportAuditId);

            $this->redirect(\App\Livewire\Accounting\Report\Index\Page::class);
        } catch (\Throwable $e) {
            $this->handleError('Prüfbericht ablehnen fehlgeschlagen', $e);
        }
    }

    public function render(): View
    {
        return view('livewire.accounting.report.audit.page');
    }
}

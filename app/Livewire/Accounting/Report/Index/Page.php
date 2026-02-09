<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Report\Index;

use App\Actions\Report\CreateAccountReportAudit;
use App\Enums\ReportStatus;
use App\Livewire\Forms\Accounting\AccountReportAuditForm;
use App\Livewire\Forms\Accounting\AccountReportForm;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\Sortable;
use App\Mail\InviteAccountAuditMemberMail;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\AccountReportAudit;
use App\Models\Membership\Member;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Page extends Component
{
    use HasPrivileges;
    use Sortable;
    use WithPagination;

    public Collection $auditorList;

    public AccountReportAuditForm $form;

    public $selectedMember;

    public AccountReport $selectedReport;

    public AccountReportForm $report;

    #[Computed]
    public function reports(): LengthAwarePaginator
    {
        return AccountReport::query()
            ->with('account')
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->paginate(10);
    }

    public function mount(): void
    {
        $this->sortBy = 'range';
        $this->auditorList = collect(); // Initialisiere eine leere Collection
    }

    public function initiateAudit(int $id): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return; // Stop execution if unauthorized
        }

        $this->selectedReport = AccountReport::find($id);

        Flux::modal('initiate-report-audit')
            ->show();
    }

    public function deletAudit(AccountReport $accountReport): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return; // Stop execution if unauthorized
        }
    }

    public function addAuditor(): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return; // Stop execution if unauthorized
        }

        if ($this->selectedMember) {
            $member = Member::find($this->selectedMember);
            if (! $this->auditorList->contains($member)) {
                $this->auditorList->push($member);
            }
        }
    }

    public function removeAuditor(int $auditorId): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return; // Stop execution if unauthorized
        }

        $member = Member::find($auditorId);
        if ($this->auditorList->contains($member)) {
            $this->auditorList->pop($auditorId);
        }
    }

    public function sendInvitations(): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return; // Stop execution if unauthorized
        }

        if ($this->auditorList->count()) {
            foreach ($this->auditorList as $auditor) {
                $this->form->account_report_id = $this->selectedReport->id;
                $this->form->user_id = $auditor->user->id;

                if ($auditor->hasUser()) {
                    $audit = CreateAccountReportAudit::handle($this->form);

                    Mail::to($auditor->email)
                        ->locale($auditor->locale)
                        ->queue(new InviteAccountAuditMemberMail($auditor, $this->selectedReport, $audit));

                    //                    $this->selectedReport->status = ReportStatus::submitted;
                    //                    $this->selectedReport->save();

                    Flux::toast(
                        text: 'Einladung an '.$auditor->email.' verschickt',
                        heading: '... ist raus',
                        variant: 'success',
                    );
                } else {
                    Flux::toast(
                        text: 'Keine E-Mail für '.$auditor->email.' gefunden',
                        heading: 'Nicht so schnell',
                        variant: 'warning',
                    );
                }
            }
        } else {
            Flux::toast(
                text: 'Es sollten noch Auditoren zur Prüfung ausgewählt werden!',
                heading: 'Nicht so schnell',
                variant: 'warning',
            );
        }
    }

    public function deleteAudit(int $auditorId): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return; // Stop execution if unauthorized
        }

        $this->selectedReport = AccountReport::query()
            ->findOrFail($auditorId);

        if ($this->selectedReport->audits->count() > 0) {
            Flux::modal('delete-report-found-audits')
                ->show();

            return;
        }

        try {
            $this->selectedReport->delete();
        } catch (\Exception $exception) {
            Flux::toast(
                text: 'Der Bericht konnte nicht gelöscht werden: '.$exception->getMessage(),
                heading: 'Fehler',
                variant: 'warning',
            );
        }
    }

    public function deleteSelectedReport(): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return; // Stop execution if unauthorized
        }

        foreach ($this->selectedReport->audits as $audit) {
            $audit->delete();
        }
        try {
            $this->selectedReport->delete();
        } catch (\Exception $exception) {
            Flux::toast(
                text: 'Der Bericht konnte nicht gelöscht werden: '.$exception->getMessage(),
                heading: 'Fehler',
                variant: 'warning',
            );
        }

        Flux::toast(
            text: 'Der Bericht wurde erfolgreich gelöscht',
            variant: 'success',
        );

        Flux::modal('delete-report-found-audits')
            ->close();
    }

    public function auditReport(int $auditId): void
    {
        $accountAudit = AccountReportAudit::query()
            ->findOrfail($auditId);
        // dd($accountAudit);
    }

    public function editReport(int $reportId): void
    {

        $this->report->set(AccountReport::query()->findOrFail($reportId));

        Flux::modal('edit-account-report')->show();

    }

    public function updateReport(): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return; // Stop execution if unauthorized
        }

        if ($this->report->update($this->report)) {
            Flux::toast(text: 'Berichtsdaten aktualisiert', variant: 'success');
            Flux::modal('edit-account-report')->close();
        } else {
            Flux::toast('Etwas ist schief gelaufen', variant: 'danger');
        }

    }

    public function updatedReportStartingAmount()
    {
        $this->calculateEndAmount();
    }

    public function updatedReportTotalIncome()
    {
        $this->calculateEndAmount();
    }

    public function updatedReportTotalExpenditure()
    {
        $this->calculateEndAmount();
    }

    private function calculateEndAmount(): void
    {
        $start = Account::makeCentInteger($this->report->starting_amount);
        $income = Account::makeCentInteger($this->report->total_income);
        $expenditure = Account::makeCentInteger($this->report->total_expenditure);

        $end = $start + $income - $expenditure;

        $this->report->end_amount = Account::formatedAmount($end);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.accounting.report.index.page')->title(__('reports.index.title'));
    }
}

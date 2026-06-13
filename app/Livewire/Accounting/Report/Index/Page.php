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
use App\Models\Accounting\DatevExport;
use App\Models\Membership\Member;
use App\Services\Accounting\Datev\DatevExportService;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
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
        $this->auditorList = collect();
    }

    public function initiateAudit(int $id): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return;
        }

        $this->selectedReport = AccountReport::with('datevExports.user')->findOrFail($id);

        // Wurde der Bericht bereits exportiert, zuerst Bestätigung einholen
        if ($this->selectedReport->wasExported()) {
            Flux::modal('reject-exported-report-confirm')->show();

            return;
        }

        Flux::modal('initiate-report-audit')->show();
    }

    /**
     * Wird aufgerufen wenn der Kassenwart im Bestätigungs-Modal trotzdem fortfahren will.
     * Öffnet dann das eigentliche Audit-Modal.
     */
    public function confirmAuditDespiteExport(): void
    {
        Flux::modal('reject-exported-report-confirm')->close();
        Flux::modal('initiate-report-audit')->show();
    }

    public function deletAudit(AccountReport $accountReport): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return;
        }
    }

    public function addAuditor(): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return;
        }

        if ($this->selectedMember) {
            $member = Member::find($this->selectedMember);
            if ($member && $member->user && $member->user->isBoardMember()) {
                Flux::toast(
                    text: __('reports.board_member_not_allowed_as_auditor'),
                    heading: __('common.error'),
                    variant: 'warning',
                );

                return;
            }
            if (! $this->auditorList->contains($member)) {
                $this->auditorList->push($member);
            }
        }
    }

    public function removeAuditor(int $auditorId): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return;
        }

        $member = Member::find($auditorId);
        if ($this->auditorList->contains($member)) {
            $this->auditorList->pop($auditorId);
        }
    }

    public function sendInvitations(): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return;
        }

        if ($this->auditorList->count() !== 0) {
            foreach ($this->auditorList as $auditor) {
                $this->form->account_report_id = $this->selectedReport->id;
                $this->form->user_id = $auditor->user->id;

                if ($auditor->hasUser()) {
                    $audit = CreateAccountReportAudit::handle($this->form);

                    Mail::to($auditor->email)
                        ->locale($auditor->locale)
                        ->queue(new InviteAccountAuditMemberMail($auditor, $this->selectedReport, $audit));

                    Flux::toast(
                        text: 'Einladung an '.$auditor->email.' verschickt',
                        heading: '... ist raus',
                        variant: 'success',
                    );
                } else {
                    Flux::toast(
                        text: __('reports.no_email_for_auditor', ['email' => $auditor->email]),
                        heading: __('common.error'),
                        variant: 'warning',
                    );
                }
            }
        } else {
            Flux::toast(
                text: __('reports.no_auditors_selected'),
                heading: __('common.error'),
                variant: 'warning',
            );
        }
    }

    public function deleteAudit(int $auditorId): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return;
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
                text: __('reports.delete_error', ['message' => $exception->getMessage()]),
                heading: __('common.error'),
                variant: 'warning',
            );
        }
    }

    public function deleteSelectedReport(): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return;
        }

        foreach ($this->selectedReport->audits as $audit) {
            $audit->delete();
        }
        try {
            $this->selectedReport->delete();
        } catch (\Exception $exception) {
            Flux::toast(
                text: __('reports.delete_error', ['message' => $exception->getMessage()]),
                heading: __('common.error'),
                variant: 'warning',
            );
        }

        Flux::toast(
            text: __('reports.delete_success'),
            variant: 'success',
        );

        Flux::modal('delete-report-found-audits')
            ->close();
    }

    public function auditReport(int $auditId): void
    {
        $accountAudit = AccountReportAudit::query()
            ->findOrfail($auditId);
    }

    public function editReport(int $reportId): void
    {
        $this->report->set(AccountReport::query()->findOrFail($reportId));

        Flux::modal('edit-account-report')->show();
    }

    public function updateReport(): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return;
        }

        if ($this->report->update($this->report)) {
            Flux::toast(text: __('reports.data_updated'), variant: 'success');
            Flux::modal('edit-account-report')->close();
        } else {
            Flux::toast('Etwas ist schief gelaufen', variant: 'danger');
        }
    }

    /**
     * Erstellt einen DATEV EXTF-Buchungsstapel für den gewählten geprüften Monatsbericht
     * und liefert die CSV als Download an den Browser.
     *
     * #[Renderless] verhindert einen Livewire-Re-render und lässt den
     * StreamedResponse direkt an den Browser durch.
     */
    #[Renderless]
    public function exportDatev(int $reportId): mixed
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return null;
        }

        /* @var AccountReport $report */
        $report = AccountReport::query()
            ->with('account')
            ->findOrFail($reportId);

        if ($report->status !== ReportStatus::audited) {
            Flux::toast(
                text: __('reports.index.datev_export.only_audited'),
                heading: __('reports.index.datev_export.not_possible'),
                variant: 'warning',
            );

            return null;
        }

        try {
            /** @var DatevExportService $service */
            $service = app(DatevExportService::class);
            $storagePath = $service->exportForReport($report);

            $filename = 'DATEV_'
                .$report->period_start->format('Y-m')
                    .'_'.str_replace(' ', '-', $report->account->name ?? __('reports.default_filename'))
                    .'.csv';

            $content = Storage::disk('local')->get('private/'.$storagePath) ?? '';

            DatevExport::create([
                'account_report_id' => $report->id,
                'exported_by' => auth()->id(),
                'filename' => $filename,
                'exported_at' => now(),
            ]);

            return response()->streamDownload(
                function () use ($content): void {
                    echo $content;
                },
                $filename,
                ['Content-Type' => 'text/csv; charset=UTF-8'],
            );
        } catch (\RuntimeException $e) {
            Flux::toast(
                text: $e->getMessage(),
                heading: __('reports.index.datev_export.failed'),
                variant: 'danger',
            );

            return null;
        }
    }

    public function updatedReportStartingAmount(): void
    {
        $this->calculateEndAmount();
    }

    public function updatedReportTotalIncome(): void
    {
        $this->calculateEndAmount();
    }

    public function updatedReportTotalExpenditure(): void
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

    public function render(): View
    {
        return view('livewire.accounting.report.index.page')->title(__('reports.index.title'));
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Accounting\Report\Index;

use App\Actions\Report\CreateAccountReportAudit;
use App\Enums\ReportStatus;
use App\Livewire\Forms\Accounting\AccountReportAuditForm;
use App\Livewire\Forms\Accounting\AccountReportForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Livewire\Traits\Sortable;
use App\Mail\InviteAccountAuditMemberMail;
use App\Models\Accounting\Account;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\AccountReportAudit;
use App\Models\Accounting\DatevExport;
use App\Models\Membership\Member;
use App\Services\Accounting\Datev\DatevCheck;
use App\Services\Accounting\Datev\DatevExportMailService;
use App\Services\Accounting\Datev\DatevExportService;
use App\Services\Accounting\Datev\DatevExportValidator;
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
    use HandlesErrors;
    use HasPrivileges;
    use Sortable;
    use WithPagination;

    public Collection $auditorList;

    public AccountReportAuditForm $form;

    public $selectedMember;

    public AccountReport $selectedReport;

    public AccountReportForm $report;

    public array $datevValidationChecks = [];

    public int $datevExportReportId = 0;

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
        try {
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
        } catch (\Throwable $e) {
            $this->handleError('Einladungen versenden fehlgeschlagen', $e);
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
        } catch (\Throwable $e) {
            $this->handleError('Bericht löschen fehlgeschlagen', $e);
        }
    }

    public function deleteSelectedReport(): void
    {
        try {
            if (! $this->checkPrivilege(AccountReport::class)) {
                return;
            }

            foreach ($this->selectedReport->audits as $audit) {
                $audit->delete();
            }
            $this->selectedReport->delete();
        } catch (\Throwable $e) {
            $this->handleError('Bericht löschen fehlgeschlagen', $e);

            return;
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
        try {
            if (! $this->checkPrivilege(AccountReport::class)) {
                return;
            }

            if ($this->report->update($this->report)) {
                Flux::toast(text: __('reports.data_updated'), variant: 'success');
                Flux::modal('edit-account-report')->close();
            } else {
                Flux::toast('Etwas ist schief gelaufen', variant: 'danger');
            }
        } catch (\Throwable $e) {
            $this->handleError('Bericht aktualisieren fehlgeschlagen', $e);
        }
    }

    /**
     * Öffnet das Validierungs-Checklisten-Modal für den DATEV Export.
     * Der eigentliche Export erfolgt erst in confirmExportDatev().
     */
    public function exportDatev(int $reportId): void
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return;
        }

        /* @var AccountReport $report */
        $report = AccountReport::query()
            ->with('account')
            ->findOrFail($reportId);

        $this->datevExportReportId = $report->id;

        /** @var DatevExportValidator $validator */
        $validator = app(DatevExportValidator::class);
        $checks = $validator->validateForReport($report);

        $this->datevValidationChecks = array_map(
            fn (DatevCheck $c) => [
                'label' => $c->label,
                'type' => $c->type->value,
                'passed' => $c->passed,
                'message' => $c->message,
            ],
            $checks,
        );

        Flux::modal('datev-export-checklist')->show();
    }

    /**
     * Führt den DATEV-Export nach erfolgreicher Validierung durch
     * und liefert die CSV als Download.
     */
    #[Renderless]
    public function confirmExportDatev(): mixed
    {
        if (! $this->checkPrivilege(AccountReport::class)) {
            return null;
        }

        /* @var AccountReport $report */
        $report = AccountReport::query()
            ->with('account')
            ->findOrFail($this->datevExportReportId);

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

            $filename = 'EXTF_Buchungsstapel_'
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
                ['Content-Type' => 'text/csv; charset=Windows-1252'],
            );
        } catch (\Throwable $e) {
            $this->handleError('DATEV-Export fehlgeschlagen', $e);

            return null;
        }
    }

    #[Renderless]
    public function sendDatevExportByEmail(): void
    {
        try {
            if (! $this->checkPrivilege(AccountReport::class)) {
                return;
            }

            $report = AccountReport::query()
                ->with('account')
                ->findOrFail($this->datevExportReportId);

            if ($report->status !== ReportStatus::audited) {
                Flux::toast(
                    text: __('reports.index.datev_export.only_audited'),
                    heading: __('reports.index.datev_export.not_possible'),
                    variant: 'warning',
                );

                return;
            }

            /** @var DatevExportMailService $service */
            $service = app(DatevExportMailService::class);
            $service->sendForReport($report);

            Flux::toast(
                text: __('reports.index.datev_export.email_sent_text'),
                heading: __('reports.index.datev_export.email_sent_heading'),
                variant: 'success',
            );

            Flux::modal('datev-export-checklist')->close();
        } catch (\Throwable $e) {
            $this->handleError('DATEV-Export-Mail fehlgeschlagen', $e);
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

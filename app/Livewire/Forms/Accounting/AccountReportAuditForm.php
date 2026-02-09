<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use App\Actions\Report\UpdateAccountReportAudit;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Accounting\AccountReportAudit;
use Flux\Flux;
use Livewire\Form;

final class AccountReportAuditForm extends Form
{
    use HasPrivileges;

    public AccountReportAudit $audit;

    public $id;

    public $account_report_id;

    public $user_id;

    public $is_approved;

    public $approved_at;

    public $reason;

    public function set(int $account_report_id): void
    {
        $this->audit = AccountReportAudit::query()->findOrFail($account_report_id);
        $this->account_report_id = $this->audit->account_report_id;
        $this->user_id = $this->audit->user_id;
        $this->is_approved = $this->audit->is_approved;
        $this->approved_at = $this->audit->approved_at;
        $this->reason = $this->audit->reason;
        $this->id = $this->audit->id;
    }

    public function create(): void
    {
        $this->checkPrivilege(AccountReportAudit::class);
        $this->validate();
    }

    public function update(): void
    {
        $this->checkPrivilege(AccountReportAudit::class);
        $this->validate();
        if (UpdateAccountReportAudit::handle($this)) {
            Flux::toast(text: 'Das Prüfergebis wurde erfasst. Vielen Dank!', variant: 'success', duration: 10000);
        } else {
            Flux::toast(text: 'Fehler beim Speichern der Prüfung!', variant: 'error', duration: 10000);
        }
    }

    protected function rules(): array
    {
        return [
            'account_report_id' => 'required|exists:account_report_audits',
            'user_id' => 'required|exists:users,id',
            'is_approved' => 'nullable|boolean',
            'approved_at' => 'nullable|date',
            'reason' => 'nullable|string',
        ];
    }

    protected function messages(): array
    {
        return [];
    }
}

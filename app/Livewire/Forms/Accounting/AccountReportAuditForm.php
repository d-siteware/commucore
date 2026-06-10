<?php

declare(strict_types=1);

namespace App\Livewire\Forms\Accounting;

use App\Actions\Report\UpdateAccountReportAudit;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Accounting\AccountReportAudit;
use Flux\Flux;
use Illuminate\Support\Carbon;
use Livewire\Form;

final class AccountReportAuditForm extends Form
{
    use HasPrivileges;

    public AccountReportAudit $audit;

    public int $id;

    public int $account_report_id;

    public int $user_id;

    public bool $is_approved;

    public Carbon $approved_at;

    public ?string $reason;

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
            Flux::toast(text: __('account_report_audit.audit_result_saved'), duration: 10000, variant: 'success');
        } else {
            Flux::toast(text: __('account_report_audit.audit_save_error'), duration: 10000, variant: 'error');
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

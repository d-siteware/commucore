<?php

declare(strict_types=1);

namespace App\Actions\Report;

use App\Livewire\Forms\Accounting\AccountReportAuditForm;
use App\Models\Accounting\AccountReportAudit;
use Carbon\Carbon;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;

final class UpdateAccountReportAudit extends Action
{
    public static function handle(AccountReportAuditForm $form): bool
    {
        return DB::transaction(function () use ($form): bool {

            return (bool) AccountReportAudit::query()->where('id', $form->id)->update([
                'account_report_id' => $form->account_report_id,
                'user_id' => $form->user_id,
                'reason' => $form->reason,
                'is_approved' => $form->is_approved,
                'approved_at' => Carbon::now('Europe/Berlin'),

            ]);
        });

    }
}

<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use App\Enums\ReportStatus;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Traits\HasHistory;
use App\Models\User;
use Database\Factories\Accounting\AccountReportFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $account_id
 * @property int $starting_amount
 * @property int $end_amount
 * @property int $created_by
 * @property Carbon $period_start
 * @property Carbon $period_end
 * @property int $total_income
 * @property int $total_expenditure
 * @property string $status
 * @property string|null $notes
 * @property-read Account $account
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AccountReportAudit> $audits
 * @property-read int|null $audits_count
 * @property-read User $user
 *
 * @method static Builder<static>|AccountReport newModelQuery()
 * @method static Builder<static>|AccountReport newQuery()
 * @method static Builder<static>|AccountReport query()
 * @method static Builder<static>|AccountReport whereAccountId($value)
 * @method static Builder<static>|AccountReport whereCreatedAt($value)
 * @method static Builder<static>|AccountReport whereCreatedBy($value)
 * @method static Builder<static>|AccountReport whereEndAmount($value)
 * @method static Builder<static>|AccountReport whereId($value)
 * @method static Builder<static>|AccountReport whereNotes($value)
 * @method static Builder<static>|AccountReport wherePeriodEnd($value)
 * @method static Builder<static>|AccountReport wherePeriodStart($value)
 * @method static Builder<static>|AccountReport whereStartingAmount($value)
 * @method static Builder<static>|AccountReport whereStatus($value)
 * @method static Builder<static>|AccountReport whereTotalExpenditure($value)
 * @method static Builder<static>|AccountReport whereTotalIncome($value)
 * @method static Builder<static>|AccountReport whereUpdatedAt($value)
 * @method static \Database\Factories\Accounting\AccountReportFactory factory($count = null, $state = [])
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\History> $histories
 * @property-read int|null $histories_count
 *
 * @mixin Eloquent
 */
final class AccountReport extends Model
{
    /** @use HasFactory<AccountReportFactory> */
    use HasFactory, HasHistory, HasPrivileges;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'starting_amount' => 'integer',
            'end_amount' => 'integer',
            'total_income' => 'integer',
            'total_expenditure' => 'integer',
            'period_start' => 'date',
            'period_end' => 'date',
            'status' => ReportStatus::class,
        ];
    }

    public function audits(): HasMany
    {
        return $this->hasMany(AccountReportAudit::class, 'account_report_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function checkAuditStatus(): bool
    {
        return $this->getReportAudits()->count() > 0;
    }

    public function getReportAudits(): \Illuminate\Database\Eloquent\Collection
    {
        return AccountReportAudit::query()->where('account_report_id', '=', $this->id)->get();
    }

    public static function setReportStatus(int $accountReportAuditId): void
    {
        $audit = AccountReportAudit::find($accountReportAuditId);

        $audits = AccountReportAudit::query()->where('account_report_id', $audit->account_report_id)->get();

        $audited_status = new Collection;

        if ($audits->count()) {
            foreach ($audits as $audit) {
                if ($audit->is_approved) {
                    $audited_status->push(true);
                }
            }
        }

        Log::debug('status', [
            'selectedAudit' => $audit,
            'audits' => $audits,
            'audits_count' => $audits->count(),
            'accountReportId' => $accountReportAuditId,
            'audit_status' => $audited_status,
            'audited_status_count' => $audited_status->count(),

        ]);

        if ($audited_status->count() === $audits->count()) {
            AccountReportAudit::query()->find($accountReportAuditId)->report()->update(['status' => ReportStatus::audited->value]);
        } else {
            AccountReportAudit::query()->find($accountReportAuditId)->report()->update(['status' => ReportStatus::submitted->value]);
        }

    }
}

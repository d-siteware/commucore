<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use App\Models\Traits\HasHistory;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $account_report_id
 * @property int $user_id
 * @property bool|null $is_approved
 * @property Carbon|null $approved_at
 * @property string|null $reason
 * @property-read AccountReport $report
 * @property-read User $user
 *
 * @method static Builder<static>|AccountReportAudit newModelQuery()
 * @method static Builder<static>|AccountReportAudit newQuery()
 * @method static Builder<static>|AccountReportAudit query()
 * @method static Builder<static>|AccountReportAudit whereAccountReportId($value)
 * @method static Builder<static>|AccountReportAudit whereApprovedAt($value)
 * @method static Builder<static>|AccountReportAudit whereCreatedAt($value)
 * @method static Builder<static>|AccountReportAudit whereId($value)
 * @method static Builder<static>|AccountReportAudit whereIsApproved($value)
 * @method static Builder<static>|AccountReportAudit whereReason($value)
 * @method static Builder<static>|AccountReportAudit whereUpdatedAt($value)
 * @method static Builder<static>|AccountReportAudit whereUserId($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\History> $histories
 * @property-read int|null $histories_count
 *
 * @mixin Eloquent
 */
final class AccountReportAudit extends Model
{
    use HasHistory;

    protected $fillable = [
        'account_report_id',
        'user_id',
        'is_approved',
        'reason',

        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(AccountReport::class, 'account_report_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isAudited(): bool
    {
        return $this->approved_at !== null;
    }
}

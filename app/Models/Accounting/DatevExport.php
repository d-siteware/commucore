<?php

declare(strict_types=1);

namespace App\Models\Accounting;

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
 * @property int $exported_by
 * @property string $filename
 * @property Carbon $exported_at
 * @property-read AccountReport $report
 * @property-read User $user
 *
 * @method static Builder<static>|DatevExport newModelQuery()
 * @method static Builder<static>|DatevExport newQuery()
 * @method static Builder<static>|DatevExport query()
 * @method static Builder<static>|DatevExport whereAccountReportId($value)
 * @method static Builder<static>|DatevExport whereExportedAt($value)
 * @method static Builder<static>|DatevExport whereExportedBy($value)
 * @method static Builder<static>|DatevExport whereFilename($value)
 * @method static Builder<static>|DatevExport whereId($value)
 *
 * @mixin Eloquent
 */
final class DatevExport extends Model
{
    protected $fillable = [
        'account_report_id',
        'exported_by',
        'filename',
        'exported_at',
    ];

    protected function casts(): array
    {
        return [
            'exported_at' => 'datetime',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(AccountReport::class, 'account_report_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'exported_by');
    }
}

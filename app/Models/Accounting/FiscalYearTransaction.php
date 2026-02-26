<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $fiscal_year_id
 * @property int $transaction_id
 * @property \Illuminate\Support\Carbon $locked_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYearTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYearTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYearTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYearTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYearTransaction whereFiscalYearId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYearTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYearTransaction whereLockedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYearTransaction whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYearTransaction whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class FiscalYearTransaction extends Pivot
{
    protected $table = 'fiscal_year_transactions';

    protected $casts = [
        'locked_at' => 'datetime',
    ];

    public $incrementing = true;
}

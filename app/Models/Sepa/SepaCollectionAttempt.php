<?php

declare(strict_types=1);

namespace App\Models\Sepa;

use App\Enums\SepaCollectionAttemptStatus;
use App\Enums\SepaSequenceType;
use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use App\Models\Membership\SepaMandate;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $member_id
 * @property int|null $sepa_mandate_id
 * @property int $amount
 * @property string $period_key
 * @property string $remittance_information
 * @property string $end_to_end_id
 * @property Carbon $due_date
 * @property SepaSequenceType $sequence_type
 * @property string|null $batch_reference
 * @property SepaCollectionAttemptStatus $status
 * @property Carbon|null $resolved_at
 * @property string|null $return_reason
 * @property int|null $transaction_id
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int $fee_year
 *
 * @method static Builder|SepaCollectionAttempt newModelQuery()
 * @method static Builder|SepaCollectionAttempt newQuery()
 * @method static Builder|SepaCollectionAttempt query()
 * @method static Builder|SepaCollectionAttempt unresolved()
 * @method static Builder|SepaCollectionAttempt forYear(int $year)
 * @method static Builder|SepaCollectionAttempt inBatch(string $batchReference)
 *
 * @mixin Eloquent
 */
final class SepaCollectionAttempt extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'due_date' => 'date',
        'resolved_at' => 'datetime',
        'status' => SepaCollectionAttemptStatus::class,
        'sequence_type' => SepaSequenceType::class,
    ];

    /**
     * @return BelongsTo<Member, $this>
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @return BelongsTo<SepaMandate, $this>
     */
    public function sepaMandate(): BelongsTo
    {
        return $this->belongsTo(SepaMandate::class);
    }

    /**
     * @return BelongsTo<Transaction, $this>
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function getFeeYearAttribute(): int
    {
        return (int) substr($this->period_key, 0, 4);
    }

    public function scopeUnresolved(Builder $query): Builder
    {
        return $query->where('status', SepaCollectionAttemptStatus::Submitted);
    }

    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where('period_key', 'like', $year.'%');
    }

    public function scopeInBatch(Builder $query, string $batchReference): Builder
    {
        return $query->where('batch_reference', $batchReference);
    }

    public function confirm(Transaction $transaction): void
    {
        $this->update([
            'status' => SepaCollectionAttemptStatus::Confirmed,
            'resolved_at' => now(),
            'transaction_id' => $transaction->id,
        ]);
    }

    public function markReturned(string $reason): void
    {
        $this->update([
            'status' => SepaCollectionAttemptStatus::Returned,
            'resolved_at' => now(),
            'return_reason' => $reason,
        ]);
    }
}

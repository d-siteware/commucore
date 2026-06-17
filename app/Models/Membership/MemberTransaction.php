<?php

declare(strict_types=1);

namespace App\Models\Membership;

use App\Enums\TransactionStatus;
use App\Models\Accounting\Transaction;
use App\Models\Event\Event;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $member_id
 * @property int $transaction_id
 * @property int|null $sepa_mandate_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Event|null $event
 * @property-read Member|null $member
 * @property-read Transaction|null $transaction
 * @property-read SepaMandate|null $sepaMandate
 *
 * @method static Builder<static>|MemberTransaction newModelQuery()
 * @method static Builder<static>|MemberTransaction newQuery()
 * @method static Builder<static>|MemberTransaction query()
 * @method static Builder<static>|MemberTransaction whereCreatedAt($value)
 * @method static Builder<static>|MemberTransaction whereId($value)
 * @method static Builder<static>|MemberTransaction whereMemberId($value)
 * @method static Builder<static>|MemberTransaction whereTransactionId($value)
 * @method static Builder<static>|MemberTransaction whereUpdatedAt($value)
 *
 * @property Carbon|null $receipt_sent_timestamp
 *
 * @method static Builder<static>|MemberTransaction whereReceiptSentTimestamp($value)
 * @method static \Database\Factories\Membership\MemberTransactionFactory factory($count = null, $state = [])
 *
 * @property bool $is_membership_fee
 * @property int|null $fee_year
 *
 * @method static Builder<static>|MemberTransaction paid()
 * @method static Builder<static>|MemberTransaction booked()
 * @method static Builder<static>|MemberTransaction forYear(int $year)
 * @method static Builder<static>|MemberTransaction membershipFees()
 * @method static Builder<static>|MemberTransaction submitted()
 * @method static Builder<static>|MemberTransaction whereFeeYear($value)
 * @method static Builder<static>|MemberTransaction whereIsMembershipFee($value)
 *
 * @mixin Eloquent
 */
final class MemberTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'is_membership_fee' => 'boolean',
        'receipt_sent_timestamp' => 'datetime',
    ];

    public function scopePaid(Builder $query): Builder
    {
        return $query->whereHas('transaction', fn ($q) => $q->where('status', TransactionStatus::booked));
    }

    // Scopes für einfache Queries
    public function scopeMembershipFees(Builder $query): Builder
    {
        return $query->where('is_membership_fee', true);
    }

    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where('fee_year', $year);
    }

    public function scopeBooked(Builder $query): Builder
    {
        return $query->whereHas('transaction', fn ($q) => $q->where('status', TransactionStatus::booked)
        );
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->whereHas('transaction', fn ($q) => $q->where('status', TransactionStatus::submitted)
        );
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function sepaMandate(): BelongsTo
    {
        return $this->belongsTo(SepaMandate::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}

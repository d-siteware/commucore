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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Event|null $event
 * @property-read Member|null $member
 * @property-read Transaction|null $transaction
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

    // Scopes für einfache Queries
    public function scopeMembershipFees($query)
    {
        return $query->where('is_membership_fee', true);
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('fee_year', $year);
    }

    public function scopeBooked($query)
    {
        return $query->whereHas('transaction', fn($q) =>
        $q->where('status', TransactionStatus::booked)
        );
    }

    public function scopeSubmitted($query)
    {
        return $query->whereHas('transaction', fn($q) =>
        $q->where('status', TransactionStatus::submitted)
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

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }


}

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
 * @property string $reason
 * @property int $user_id
 * @property int $transaction_id
 * @property int|null $reversal_transaction_id
 * @property string $status
 * @property-read Transaction|null $transaction
 * @property-read Transaction|null $reversalTransaction
 * @property-read User|null $user
 *
 * @method static Builder<static>|CancelTransaction newModelQuery()
 * @method static Builder<static>|CancelTransaction newQuery()
 * @method static Builder<static>|CancelTransaction query()
 * @method static Builder<static>|CancelTransaction whereCreatedAt($value)
 * @method static Builder<static>|CancelTransaction whereId($value)
 * @method static Builder<static>|CancelTransaction whereReason($value)
 * @method static Builder<static>|CancelTransaction whereStatus($value)
 * @method static Builder<static>|CancelTransaction whereTransactionId($value)
 * @method static Builder<static>|CancelTransaction whereUpdatedAt($value)
 * @method static Builder<static>|CancelTransaction whereUserId($value)
 *
 * @mixin Eloquent
 */
final class CancelTransaction extends Model
{
    protected $guarded = [];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function reversalTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'reversal_transaction_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

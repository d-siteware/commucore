<?php

declare(strict_types=1);

namespace App\Models\Funding;

use App\Models\Accounting\Transaction;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $funding_id
 * @property int $transaction_id
 * @property int|null $allocated_amount Teilbetrag in Cent – null = volle Transaktion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Funding $funding
 * @property-read Transaction $transaction
 *
 * @method static Builder<static>|FundingTransaction newModelQuery()
 * @method static Builder<static>|FundingTransaction newQuery()
 * @method static Builder<static>|FundingTransaction query()
 * @method static Builder<static>|FundingTransaction whereId($value)
 * @method static Builder<static>|FundingTransaction whereFundingId($value)
 * @method static Builder<static>|FundingTransaction whereTransactionId($value)
 * @method static Builder<static>|FundingTransaction whereAllocatedAmount($value)
 * @method static Builder<static>|FundingTransaction whereCreatedAt($value)
 * @method static Builder<static>|FundingTransaction whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class FundingTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'allocated_amount' => 'integer',
    ];

    public function funding(): BelongsTo
    {
        return $this->belongsTo(Funding::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Effektiver Betrag in Cent:
     * allocated_amount wenn gesetzt, sonst voller Transaktionsbetrag.
     */
    public function effectiveAmount(): int
    {
        return $this->allocated_amount ?? $this->transaction->amount_gross;
    }
}

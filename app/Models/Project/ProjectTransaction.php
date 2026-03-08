<?php

declare(strict_types=1);

namespace App\Models\Project;

use App\Models\Accounting\Transaction;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $project_id
 * @property int $transaction_id
 * @property int|null $allocated_amount Teilbetrag in Cent – null = volle Transaktion
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Project $project
 * @property-read Transaction $transaction
 *
 * @method static Builder<static>|ProjectTransaction newModelQuery()
 * @method static Builder<static>|ProjectTransaction newQuery()
 * @method static Builder<static>|ProjectTransaction query()
 * @method static Builder<static>|ProjectTransaction whereId($value)
 * @method static Builder<static>|ProjectTransaction whereProjectId($value)
 * @method static Builder<static>|ProjectTransaction whereTransactionId($value)
 * @method static Builder<static>|ProjectTransaction whereAllocatedAmount($value)
 * @method static Builder<static>|ProjectTransaction whereCreatedAt($value)
 * @method static Builder<static>|ProjectTransaction whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class ProjectTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'allocated_amount' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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

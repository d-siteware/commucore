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
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property int $funding_id
 * @property int $transaction_id
 * @property int|null $allocated_amount Teilbetrag in Cent – null = volle Transaktion
 * @property int|null $funding_position_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Funding $funding
 * @property-read Transaction $transaction
 * @property-read FundingPosition|null $fundingPosition
 *
 * @method static Builder<static>|FundingTransaction newModelQuery()
 * @method static Builder<static>|FundingTransaction newQuery()
 * @method static Builder<static>|FundingTransaction query()
 * @method static Builder<static>|FundingTransaction whereId($value)
 * @method static Builder<static>|FundingTransaction whereFundingId($value)
 * @method static Builder<static>|FundingTransaction whereTransactionId($value)
 * @method static Builder<static>|FundingTransaction whereAllocatedAmount($value)
 * @method static Builder<static>|FundingTransaction whereFundingPositionId($value)
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

    protected static function booted(): void
    {
        // Invariante Mehrfachförderung: sobald eine Buchung an MEHR als einer
        // Förderung hängt, muss JEDE Zeile einen allocated_amount tragen –
        // sonst fällt effectiveAmount() auf den vollen Bruttobetrag zurück
        // (Überzählung in Reports). Guard bewusst auf Model-Ebene (nicht nur
        // im UI-Flow), damit auch Action-, Seeder- oder künftige Edit-Pfade
        // die Hintertür nicht öffnen können.
        static::saving(function (FundingTransaction $ft): void {
            $siblings = static::query()
                ->where('transaction_id', $ft->transaction_id)
                ->when($ft->exists, fn ($q) => $q->whereKeyNot($ft->getKey()))
                ->get(['allocated_amount']);

            if ($siblings->isEmpty()) {
                return;
            }

            $violates = $ft->allocated_amount === null
                || $siblings->contains(fn (FundingTransaction $sibling): bool => $sibling->allocated_amount === null);

            if ($violates) {
                throw ValidationException::withMessages([
                    'allocated_amount' => __('transaction.validation.funding_transaction.allocated_required_multi'),
                ]);
            }
        });
    }

    public function funding(): BelongsTo
    {
        return $this->belongsTo(Funding::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function fundingPosition(): BelongsTo
    {
        return $this->belongsTo(FundingPosition::class);
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

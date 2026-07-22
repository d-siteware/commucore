<?php

declare(strict_types=1);

namespace App\Models\Funding;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Membership\Member;
use Database\Factories\Funding\FundingPositionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $funding_id
 * @property string $title
 * @property int $budget Plan-Budget brutto in Cent
 * @property int|null $funding_position_category_id
 * @property int|null $member_id Verantwortlicher
 * @property Carbon|null $due_date
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Funding $funding
 * @property-read FundingPositionCategory|null $category
 * @property-read Member|null $responsible
 * @property-read Collection<int, FundingTransaction> $fundingTransactions
 * @property-read int|null $funding_transactions_count
 *
 * @method static FundingPositionFactory factory($count = null, $state = [])
 * @method static Builder<static>|FundingPosition newModelQuery()
 * @method static Builder<static>|FundingPosition newQuery()
 * @method static Builder<static>|FundingPosition query()
 * @method static Builder<static>|FundingPosition whereId($value)
 * @method static Builder<static>|FundingPosition whereFundingId($value)
 * @method static Builder<static>|FundingPosition whereTitle($value)
 * @method static Builder<static>|FundingPosition whereBudget($value)
 * @method static Builder<static>|FundingPosition whereFundingPositionCategoryId($value)
 * @method static Builder<static>|FundingPosition whereMemberId($value)
 * @method static Builder<static>|FundingPosition whereDueDate($value)
 * @method static Builder<static>|FundingPosition whereCreatedAt($value)
 * @method static Builder<static>|FundingPosition whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class FundingPosition extends Model
{
    /** @use HasFactory<FundingPositionFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'budget' => 'integer',
        'due_date' => 'date',
    ];

    // ==================== Relationships ====================

    public function funding(): BelongsTo
    {
        return $this->belongsTo(Funding::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FundingPositionCategory::class, 'funding_position_category_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function fundingTransactions(): HasMany
    {
        return $this->hasMany(FundingTransaction::class);
    }

    // ==================== Methods ====================

    /**
     * Ist-Ausgaben in Cent: Summe der gebuchten Ausgaben, die über die
     * funding_transactions-Verknüpfung dieser Position zugeordnet sind.
     */
    public function actualAmount(): int
    {
        /** @var Collection<int, FundingTransaction> $items */
        $items = $this->fundingTransactions()
            ->with('transaction')
            ->whereHas('transaction', fn (Builder $q) => $q
                ->where('status', TransactionStatus::booked->value)
                ->where('type', TransactionType::Withdrawal->value)
            )
            ->get();

        return $items->sum(fn (FundingTransaction $ft): int => $ft->effectiveAmount());
    }

    /**
     * Abweichung Plan/Ist in Cent (positiv = Restbudget, negativ = überzogen).
     */
    public function remainingBudget(): int
    {
        return $this->budget - $this->actualAmount();
    }
}

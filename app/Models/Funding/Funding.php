<?php

declare(strict_types=1);

namespace App\Models\Funding;

use App\Enums\FundingStatus;
use App\Models\Accounting\BookingAccount;
use Database\Factories\Funding\FundingFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $funder Name des Fördergebers
 * @property string|null $description
 * @property FundingStatus $status
 * @property int|null $approved_amount Bewilligter Gesamtbetrag in Cent
 * @property Carbon|null $funding_period_start
 * @property Carbon|null $funding_period_end
 * @property string|null $reference Aktenzeichen / Fördernummer
 * @property int|null $booking_account_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BookingAccount|null $bookingAccount
 * @property-read Collection<int, FundingTransaction> $fundingTransactions
 * @property-read int|null $funding_transactions_count
 * @property-read Collection<int, \App\Models\Project\Project> $projects
 * @property-read int|null $projects_count
 *
 * @method static FundingFactory factory($count = null, $state = [])
 * @method static Builder<static>|Funding newModelQuery()
 * @method static Builder<static>|Funding newQuery()
 * @method static Builder<static>|Funding query()
 * @method static Builder<static>|Funding whereId($value)
 * @method static Builder<static>|Funding whereTitle($value)
 * @method static Builder<static>|Funding whereFunder($value)
 * @method static Builder<static>|Funding whereStatus($value)
 * @method static Builder<static>|Funding whereApprovedAmount($value)
 * @method static Builder<static>|Funding whereFundingPeriodStart($value)
 * @method static Builder<static>|Funding whereFundingPeriodEnd($value)
 * @method static Builder<static>|Funding whereReference($value)
 * @method static Builder<static>|Funding whereBookingAccountId($value)
 * @method static Builder<static>|Funding whereCreatedAt($value)
 * @method static Builder<static>|Funding whereUpdatedAt($value)
 * @method static Builder<static>|Funding active()
 * @method static Builder<static>|Funding inYear(int $year)
 *
 * @mixin Eloquent
 */
final class Funding extends Model
{
    /** @use HasFactory<FundingFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => FundingStatus::class,
        'approved_amount' => 'integer',
        'funding_period_start' => 'date',
        'funding_period_end' => 'date',
    ];

    // ==================== Relationships ====================

    public function bookingAccount(): BelongsTo
    {
        return $this->belongsTo(BookingAccount::class);
    }

    public function fundingTransactions(): HasMany
    {
        return $this->hasMany(FundingTransaction::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Project\Project::class,
            'project_fundings'
        )
            ->withPivot('allocated_amount')
            ->withTimestamps();
    }

    // ==================== Scopes ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', FundingStatus::Active);
    }

    /**
     * Förderungen die in einem Jahr aktiv waren.
     */
    public function scopeInYear(Builder $query, int $year): Builder
    {
        return $query
            ->where(fn (Builder $q) => $q
                ->whereNull('funding_period_start')
                ->orWhere('funding_period_start', '<=', "{$year}-12-31")
            )
            ->where(fn (Builder $q) => $q
                ->whereNull('funding_period_end')
                ->orWhere('funding_period_end', '>=', "{$year}-01-01")
            );
    }

    // ==================== Methods ====================

    /**
     * Summe der tatsächlich erhaltenen Einnahmen in Cent.
     */
    public function totalReceived(): int
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, FundingTransaction> $items */
        $items = $this->fundingTransactions()->with('transaction')->get();

        return $items->sum(fn (FundingTransaction $ft): int => $ft->allocated_amount ?? $ft->transaction->amount_gross
        );
    }

    /**
     * Noch nicht verbrauchter Betrag in Cent.
     * approved_amount - Summe der allocated_amounts aller zugeordneten Projekte.
     */
    public function remainingAmount(): int
    {
        $used = (int) $this->projects()->sum('project_fundings.allocated_amount');

        return ($this->approved_amount ?? 0) - $used;
    }

    /**
     * Verwendungsnachweis: bewilligt vs. verwendet vs. erhalten.
     *
     * @return array{approved: int, allocated_to_projects: int, received: int, remaining: int}
     */
    public function usageReport(): array
    {
        $allocated = (int) $this->projects()->sum('project_fundings.allocated_amount');
        $received = $this->totalReceived();
        $approved = $this->approved_amount ?? 0;

        return [
            'approved' => $approved,
            'allocated_to_projects' => $allocated,
            'received' => $received,
            'remaining' => $approved - $allocated,
        ];
    }
}

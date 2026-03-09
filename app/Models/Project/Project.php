<?php

declare(strict_types=1);

namespace App\Models\Project;

use App\Enums\ProjectStatus;
use App\Models\Accounting\BookingAccount;
use Database\Factories\Project\ProjectFactory;
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
 * @property string|null $description
 * @property ProjectStatus $status
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property int|null $booking_account_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BookingAccount|null $bookingAccount
 * @property-read Collection<int, ProjectTransaction> $projectTransactions
 * @property-read int|null $project_transactions_count
 * @property-read Collection<int, \App\Models\Funding\Funding> $fundings
 * @property-read int|null $fundings_count
 *
 * @method static ProjectFactory factory($count = null, $state = [])
 * @method static Builder<static>|Project newModelQuery()
 * @method static Builder<static>|Project newQuery()
 * @method static Builder<static>|Project query()
 * @method static Builder<static>|Project whereId($value)
 * @method static Builder<static>|Project whereTitle($value)
 * @method static Builder<static>|Project whereStatus($value)
 * @method static Builder<static>|Project whereStartDate($value)
 * @method static Builder<static>|Project whereEndDate($value)
 * @method static Builder<static>|Project whereBookingAccountId($value)
 * @method static Builder<static>|Project whereCreatedAt($value)
 * @method static Builder<static>|Project whereUpdatedAt($value)
 * @method static Builder<static>|Project active()
 * @method static Builder<static>|Project inYear(int $year)
 *
 * @mixin Eloquent
 */
final class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'status' => ProjectStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // ==================== Relationships ====================

    public function bookingAccount(): BelongsTo
    {
        return $this->belongsTo(BookingAccount::class);
    }

    public function projectTransactions(): HasMany
    {
        return $this->hasMany(ProjectTransaction::class);
    }

    /** @return BelongsToMany<\App\Models\Funding\Funding, $this> */
    public function fundings(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Funding\Funding::class,
            'project_fundings'
        )
            ->withPivot('allocated_amount')
            ->withTimestamps();
    }

    // ==================== Scopes ====================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ProjectStatus::Active);
    }

    /**
     * Projekte die in einem Jahr aktiv waren
     * (start_date <= Jahr-Ende UND (end_date >= Jahr-Start ODER end_date IS NULL))
     */
    public function scopeInYear(Builder $query, int $year): Builder
    {
        return $query
            ->where(fn (Builder $q) => $q
                ->whereNull('start_date')
                ->orWhere('start_date', '<=', "{$year}-12-31")
            )
            ->where(fn (Builder $q) => $q
                ->whereNull('end_date')
                ->orWhere('end_date', '>=', "{$year}-01-01")
            );
    }

    // ==================== Methods ====================

    /**
     * Summe aller Ausgaben (allocated oder voll) in Cent.
     */
    public function totalExpense(): int
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, ProjectTransaction> $items */
        $items = $this->projectTransactions()->with('transaction')->get();

        return $items->sum(fn (ProjectTransaction $pt): int => $pt->allocated_amount ?? $pt->transaction->amount_gross
        );
    }

    /**
     * Summe aller zugewiesenen Fördermittel in Cent.
     */
    public function totalFundingAllocated(): int
    {
        return (int) $this->fundings()->sum('project_fundings.allocated_amount');
    }
}

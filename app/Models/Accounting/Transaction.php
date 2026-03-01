<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Event\EventTransaction;
use App\Models\Event\EventVisitor;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use App\Models\Traits\HasHistory;
use Database\Factories\Accounting\TransactionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property Carbon $date
 * @property string $label
 * @property string|null $reference
 * @property string|null $description
 * @property int $amount_gross
 * @property int $vat
 * @property int|null $tax
 * @property int $amount_net
 * @property int $account_id
 * @property int|null $booking_account_id
 * @property TransactionType $type
 * @property TransactionStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account $account
 * @property-read EventTransaction|null $event_transaction
 * @property-read MemberTransaction|null $member_transaction
 * @property-read Member|null $members
 * @property-read Collection<int, Receipt> $receipts
 * @property-read int|null $receipts_count
 * @property-read Collection<int, EventVisitor> $visitors
 * @property-read int|null $visitors_count
 *
 * @method static TransactionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Transaction newModelQuery()
 * @method static Builder<static>|Transaction newQuery()
 * @method static Builder<static>|Transaction query()
 * @method static Builder<static>|Transaction whereAccountId($value)
 * @method static Builder<static>|Transaction whereAmountGross($value)
 * @method static Builder<static>|Transaction whereAmountNet($value)
 * @method static Builder<static>|Transaction whereBookingAccountId($value)
 * @method static Builder<static>|Transaction whereCreatedAt($value)
 * @method static Builder<static>|Transaction whereDate($value)
 * @method static Builder<static>|Transaction whereDescription($value)
 * @method static Builder<static>|Transaction whereId($value)
 * @method static Builder<static>|Transaction whereLabel($value)
 * @method static Builder<static>|Transaction whereReference($value)
 * @method static Builder<static>|Transaction whereStatus($value)
 * @method static Builder<static>|Transaction whereTax($value)
 * @method static Builder<static>|Transaction whereType($value)
 * @method static Builder<static>|Transaction whereUpdatedAt($value)
 * @method static Builder<static>|Transaction whereVat($value)
 *
 * @property-read Collection<int, \App\Models\History> $histories
 * @property-read int|null $histories_count
 *
 * @method static Builder<static>|Transaction lockedInYear(int $year)
 * @method static Builder<static>|Transaction unlocked(int $year)
 * @method static Builder<static>|Transaction whereYearEquals(int $year)
 * @method static Builder<static>|Transaction whereYearMonth(int $year, int $month)
 * @method static Builder<static>|Transaction distinctYears()
 *
 * @property-read \App\Models\Accounting\FiscalYearTransaction|null $pivot
 * @property-read Collection<int, \App\Models\Accounting\FiscalYear> $fiscalYears
 * @property-read int|null $fiscal_years_count
 *
 * @mixin Eloquent
 */
final class Transaction extends Model
{
    /** @use HasFactory<TransactionFactory> */
    use HasFactory;

    use HasHistory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime',
        'amount_gross' => 'integer',
        'amount_net' => 'integer',
        'status' => TransactionStatus::class,
        'type' => TransactionType::class,
    ];

    private TransactionHelper $transactionHelper;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->transactionHelper = new TransactionHelper($this);
    }

    // ==================== Relationships ====================

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function event_transaction(): HasOne
    {
        return $this->hasOne(EventTransaction::class);
    }

    public function member_transaction(): HasOne
    {
        return $this->hasOne(MemberTransaction::class);
    }

    public function members(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(EventVisitor::class);
    }

    public function fiscalYears(): BelongsToMany
    {
        return $this->belongsToMany(FiscalYear::class, 'fiscal_year_transactions')
            ->using(FiscalYearTransaction::class)
            ->withPivot('locked_at')
            ->withTimestamps();
    }

    // ==================== Scopes ====================

    /**
     * Scope: Nur ungesperrte Transaktionen für ein Jahr
     */
    public function scopeUnlocked(Builder $query, int $year): Builder
    {
        return $query->whereDoesntHave('fiscalYears', function ($q) use ($year) {
            $q->where('year', $year);
        });
    }

    /**
     * Scope: Nur gesperrte Transaktionen für ein Jahr
     */
    public function scopeLockedInYear(Builder $query, int $year): Builder
    {
        return $query->whereHas('fiscalYears', function ($q) use ($year) {
            $q->where('year', $year);
        });
    }

    /**
     * Scope: Filter by year (database-agnostic)
     *
     * Funktioniert mit SQLite, MySQL, PostgreSQL
     */
    public function scopeWhereYearEquals(Builder $query, int $year): Builder
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'sqlite' => $query->whereRaw("strftime('%Y', date) = ?", [(string) $year]),
            'pgsql' => $query->whereRaw('EXTRACT(YEAR FROM date) = ?', [$year]),
            default => $query->whereYear('date', $year), // MySQL/MariaDB
        };
    }

    /**
     * Scope: Get distinct years from transactions (database-agnostic)
     *
     * Usage: Transaction::distinctYears()->orderBy('year', 'desc')->pluck('year')
     */
    public function scopeDistinctYears(Builder $query): Builder
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            return $query->selectRaw("DISTINCT strftime('%Y', date) as year");
        } elseif ($driver === 'pgsql') {
            return $query->selectRaw('DISTINCT EXTRACT(YEAR FROM date) as year');
        }

        // MySQL/MariaDB
        return $query->selectRaw('DISTINCT YEAR(date) as year');
    }

    /**
     * Scope: Filter by month and year (database-agnostic)
     */
    public function scopeWhereYearMonth(Builder $query, int $year, int $month): Builder
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'sqlite' => $query->whereRaw("strftime('%Y', date) = ? AND strftime('%m', date) = ?", [
                (string) $year,
                str_pad((string) $month, 2, '0', STR_PAD_LEFT),
            ]),
            'pgsql' => $query->whereRaw('EXTRACT(YEAR FROM date) = ? AND EXTRACT(MONTH FROM date) = ?', [
                $year,
                $month,
            ]),
            default => $query->whereYear('date', $year)->whereMonth('date', $month),
        };
    }

    // ==================== Methods ====================

    public function grossForHumans(): string
    {
        return $this->transactionHelper->grossForHumans();
    }

    public function taxForHumans(): string
    {
        return $this->transactionHelper->taxForHumans();
    }

    public function netForHumans(): string
    {
        return $this->transactionHelper->netForHumans();
    }

    public function grossColor(): string
    {
        return $this->type->color();
    }

    /**
     * Prüfe ob Transaction in einem bestimmten FY gesperrt ist
     */
    public function isLockedInFiscalYear(int $year): bool
    {
        return $this->fiscalYears()
            ->where('year', $year)
            ->exists();
    }

    /**
     * Hole den Zeitpunkt der Sperrung für ein FY
     */
    public function getLockedAtForFiscalYear(int $year): ?string
    {
        $fiscalYear = $this->fiscalYears()
            ->where('year', $year)
            ->first();

        if (! $fiscalYear) {
            return null;
        }

        /** @var FiscalYearTransaction $pivot */
        $pivot = $fiscalYear->pivot;

        return $pivot->locked_at->toDateTimeString();
    }

    /**
     * Hole das gesperrte FiscalYear mit Pivot-Daten
     */
    public function getLockedFiscalYear(int $year): ?FiscalYearTransaction
    {
        $fiscalYear = $this->fiscalYears()
            ->where('year', $year)
            ->first();

        /** @var FiscalYearTransaction|null */
        return $fiscalYear?->pivot;
    }

    /**
     * Prüfe ob Transaction bearbeitbar ist
     */
    public function isEditable(): bool
    {
        $currentYear = (int) session('financialYear');

        // Gesperrte Transaktionen sind nicht bearbeitbar
        if ($this->isLockedInFiscalYear($currentYear)) {
            return false;
        }

        return true;
    }
}

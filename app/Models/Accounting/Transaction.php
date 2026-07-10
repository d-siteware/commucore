<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use App\Enums\BookingAccountArea;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Contracts\HasDocuments as HasDocumentsContract;
use App\Models\Event\EventTransaction;
use App\Models\Event\EventVisitor;
use App\Models\Funding\FundingTransaction;
use App\Models\History;
use App\Models\Membership\Member;
use App\Models\Membership\MemberTransaction;
use App\Models\Project\ProjectTransaction;
use App\Models\Sepa\SepaCollectionAttempt;
use App\Models\Traits\HasDocuments;
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
 * @property int $amount_gross Bruttobetrag in Cent (Quelle der Wahrheit)
 * @property int $vat MwSt-Satz in Prozent (0, 7 oder 19)
 * @property int $amount_net Nettobetrag in Cent
 * @property int $tax Steuerbetrag in Cent – BERECHNET (amount_gross - amount_net), nicht in DB
 * @property int $account_id
 * @property int|null $booking_account_id
 * @property TransactionType $type
 * @property TransactionStatus $status
 * @property BookingAccountArea|null $area Steuerliche Sphäre (KOST1) – nullable bis Migration befüllt
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account $account
 * @property-read BookingAccount|null $bookingAccount
 * @property-read EventTransaction|null $event_transaction
 * @property-read MemberTransaction|null $member_transaction
 * @property-read Member|null $members
 * @property-read Collection<int, Receipt> $receipts
 * @property-read int|null $receipts_count
 * @property-read Collection<int, EventVisitor> $visitors
 * @property-read int|null $visitors_count
 * @property-read Collection<int, FiscalYear> $fiscalYears
 * @property-read int|null $fiscal_years_count
 * @property-read FiscalYearTransaction|null $pivot
 * @property-read Collection<int, History> $histories
 * @property-read int|null $histories_count
 * @property-read ProjectTransaction|null $project_transaction
 * @property-read FundingTransaction|null $funding_transaction
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
 * @method static Builder<static>|Transaction whereType($value)
 * @method static Builder<static>|Transaction whereUpdatedAt($value)
 * @method static Builder<static>|Transaction whereVat($value)
 * @method static Builder<static>|Transaction lockedInYear(int $year)
 * @method static Builder<static>|Transaction unlocked(int $year)
 * @method static Builder<static>|Transaction whereYearEquals(int $year)
 * @method static Builder<static>|Transaction whereYearMonth(int $year, int $month)
 * @method static Builder<static>|Transaction distinctYears()
 *
 * @mixin Eloquent
 */
final class Transaction extends Model implements HasDocumentsContract
{
    use HasDocuments;

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
        'area' => BookingAccountArea::class,
    ];

    private TransactionHelper $transactionHelper;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->transactionHelper = new TransactionHelper($this);
    }

    // ==================== Accessors ====================

    /**
     * Steuerbetrag in Cent.
     *
     * Nicht in der Datenbank gespeichert – wird exakt aus Brutto und Netto
     * berechnet. Keine Rundungsdifferenzen möglich, da:
     *   amount_gross = amount_net + tax (per Definition beim Erfassen)
     *
     * Bleibt als Accessor erhalten, damit bestehender Code (inkl.
     * TransactionHelper::taxForHumans) ohne Änderung funktioniert.
     */
    public function getTaxAttribute(): int
    {
        return $this->amount_gross - $this->amount_net;
    }

    // ==================== Relationships ====================

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function bookingAccount(): BelongsTo
    {
        return $this->belongsTo(BookingAccount::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    public function event_transaction(): HasOne
    {
        return $this->hasOne(EventTransaction::class);
    }

    public function project_transaction(): HasOne
    {
        return $this->hasOne(ProjectTransaction::class);
    }

    public function funding_transaction(): HasOne
    {
        return $this->hasOne(FundingTransaction::class);
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

    /**
     * Storno-Audit-Eintrag, falls diese Transaktion storniert wurde.
     */
    public function cancellation(): HasOne
    {
        return $this->hasOne(CancelTransaction::class);
    }

    /**
     * Storno-Audit-Eintrag, falls diese Transaktion selbst die
     * Storno-Gegenbuchung einer anderen Transaktion ist.
     */
    public function reversalOf(): HasOne
    {
        return $this->hasOne(CancelTransaction::class, 'reversal_transaction_id');
    }

    /**
     * Wurde diese Transaktion storniert oder ist sie selbst eine
     * Storno-Gegenbuchung? Dann sind Storno/Textänderung/Umbuchung gesperrt.
     */
    public function isCancellationLocked(): bool
    {
        if ($this->relationLoaded('cancellation') && $this->relationLoaded('reversalOf')) {
            return $this->cancellation !== null || $this->reversalOf !== null;
        }

        return $this->cancellation()->exists() || $this->reversalOf()->exists();
    }

    // ==================== Scopes ====================

    public function scopeBooked(Builder $query): Builder
    {
        return $query->where('status', TransactionStatus::booked->value);
    }

    public function scopeFinancialReportable(Builder $query): Builder
    {
        return $query
            ->where('status', TransactionStatus::booked->value)
            ->whereNot('type', TransactionType::Transfer->value);
    }

    public function scopeDatevExportable(Builder $query): Builder
    {
        return $query
            ->where('status', TransactionStatus::booked->value)
            ->whereNot('type', TransactionType::Transfer->value)
            ->whereNotNull('booking_account_id');
    }

    public function scopeUnlocked(Builder $query, int $year): Builder
    {
        return $query->whereDoesntHave('fiscalYears', function ($q) use ($year): void {
            $q->where('year', $year);
        });
    }

    public function scopeLockedInYear(Builder $query, int $year): Builder
    {
        return $query->whereHas('fiscalYears', function ($q) use ($year): void {
            $q->where('year', $year);
        });
    }

    public function scopeWhereYearEquals(Builder $query, int $year): Builder
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'sqlite' => $query->whereRaw("strftime('%Y', date) = ?", [(string) $year]),
            'pgsql' => $query->whereRaw('EXTRACT(YEAR FROM date) = ?', [$year]),
            default => $query->whereYear('date', $year),
        };
    }

    public function scopeDistinctYears(Builder $query): Builder
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'sqlite' => $query->selectRaw("DISTINCT strftime('%Y', date) as year"),
            'pgsql' => $query->selectRaw('DISTINCT EXTRACT(YEAR FROM date) as year'),
            default => $query->selectRaw('DISTINCT YEAR(date) as year'),
        };
    }

    public function scopeWhereYearMonth(Builder $query, int $year, int $month): Builder
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'sqlite' => $query->whereRaw(
                "strftime('%Y', date) = ? AND strftime('%m', date) = ?",
                [(string) $year, str_pad((string) $month, 2, '0', STR_PAD_LEFT)]
            ),
            'pgsql' => $query->whereRaw(
                'EXTRACT(YEAR FROM date) = ? AND EXTRACT(MONTH FROM date) = ?',
                [$year, $month]
            ),
            default => $query->whereYear('date', $year)->whereMonth('date', $month),
        };
    }

    // ==================== Methods ====================

    public function grossForHumans(bool $withSign = true): string
    {
        return $this->transactionHelper->grossForHumans($withSign);
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

    public function isLockedInFiscalYear(int $year): bool
    {
        return $this->fiscalYears()
            ->where('year', $year)
            ->exists();
    }

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

    public function getLockedFiscalYear(int $year): ?FiscalYearTransaction
    {
        $fiscalYear = $this->fiscalYears()
            ->using(FiscalYearTransaction::class)
            ->where('year', $year)
            ->first();

        return $fiscalYear?->pivot;
    }

    public function isEditable(): bool
    {
        $currentYear = (int) session('financialYear');

        return ! $this->isLockedInFiscalYear($currentYear);
    }

    /* --------------   SEPA.  --------------------

    */

    public function SepaCollectionAttempt(): HasOne
    {
        return $this->hasOne(SepaCollectionAttempt::class);
    }
}

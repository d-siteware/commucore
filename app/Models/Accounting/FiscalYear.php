<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use App\Models\Concerns\InvalidatesOnboardingStatus;
use App\Models\User;
use App\Services\Accounting\FiscalYearService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $year
 * @property Carbon $opened_at
 * @property Carbon|null $closed_at
 * @property int|null $opened_by
 * @property int|null $closed_by
 * @property int|null $booking_account_type_id
 * @property string|null $annual_report_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BookingAccountType|null $bookingAccountType
 * @property-read User|null $closedBy
 * @property-read User|null $openedBy
 * @property-read Collection<int, Transaction> $transactions
 * @property-read int|null $transactions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYear newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYear newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYear query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYear whereClosedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYear whereClosedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYear whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYear whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYear whereOpenedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYear whereOpenedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYear whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FiscalYear whereYear($value)
 *
 * @property-read FiscalYearTransaction|null $pivot
 *
 * @method static \Database\Factories\Accounting\FiscalYearFactory factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
final class FiscalYear extends Model
{
    use HasFactory;
    use InvalidatesOnboardingStatus;

    protected $fillable = [
        'year',
        'opened_at',
        'closed_at',
        'opened_by',
        'closed_by',
        'annual_report_path',
        'booking_account_type_id',
    ];

    protected $casts = [
        'year' => 'integer',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'fiscal_year_transactions')
            ->using(FiscalYearTransaction::class)
            ->withPivot('locked_at')
            ->withTimestamps();
    }

    public function bookingAccountType(): BelongsTo
    {
        return $this->belongsTo(BookingAccountType::class);
    }

    public function balance(): int
    {
        $service = new FiscalYearService;

        $snapshot = $service->getSnapshot($this->year);

        return $snapshot['summary']['balance'];
    }

    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }

    public function isOpen(): bool
    {
        return $this->closed_at === null;
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->whereNotNull('closed_at');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNull('closed_at');
    }

    /**
     * Hole das aktuell aktive Geschäftsjahr.
     *
     * Semantik: das neueste NICHT-ZUKÜNFTIGE offene Jahr (geklemmt aufs
     * Kalenderjahr in der Accounting-Timezone). Damit wird ein per
     * Vorausbuchung angelegtes Zukunfts-FY nie aktiv, bis der Jahreswechsel
     * es natürlich einholt – und offene Backfill-Altjahre gewinnen nie
     * gegen das aktuelle Jahr.
     */
    public static function getActive(): ?self
    {
        return self::whereNull('closed_at')
            ->where('year', '<=', now(config('commucore.accounting_timezone'))->year)
            ->orderByDesc('year')
            ->first();
    }

    /**
     * Hole das Geschäftsjahr aus der Session (vom FiscalYearSwitcher gesetzt).
     */
    public static function getCurrent(): ?self
    {
        $id = session('fiscalYearId');

        return $id ? self::find($id) : null;
    }

    /**
     * Kontenrahmen-Kontext: das Geschäftsjahr, in das gebucht bzw. dessen
     * Kontext angezeigt wird. Session-FY vor aktivem FY – der Fallback ist
     * tragend für session-lose Kontexte (Artisan, Seeder, Scheduler).
     */
    public static function contextFiscalYear(): ?self
    {
        return self::getCurrent() ?? self::getActive();
    }

    /**
     * Hole oder erstelle ein Geschäftsjahr
     */
    public static function getOrCreate(int $year, ?int $userId = null): self
    {
        $fiscalYear = self::firstOrCreate(
            ['year' => $year],
            [
                'opened_at' => now(),
                'opened_by' => $userId ?? auth()->id(),
            ],
        );

        if ($fiscalYear->wasRecentlyCreated && $fiscalYear->booking_account_type_id === null) {
            $previousYear = self::where('year', $year - 1)->first();
            $fiscalYear->update([
                'booking_account_type_id' => $previousYear?->booking_account_type_id,
            ]);
        }

        return $fiscalYear->fresh();
    }
}

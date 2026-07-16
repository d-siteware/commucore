<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use App\Enums\AccountCategory;
use App\Enums\AccountSubtype;
use App\Enums\BookingAccountArea;
use Database\Factories\Accounting\BookingAccountFactory;
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
 * @property string $number SKR42-Kontonummer (5-stellig, z.B. "16100")
 * @property string $label Kontobezeichnung auf Deutsch
 * @property BookingAccountArea $area Steuerliche Sphäre
 * @property AccountCategory $category Buchhalterische Grundkategorie
 * @property AccountSubtype|null $subtype Untertyp – nur für operative Konten
 * @property int|null $booking_account_type_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BookingAccountType|null $bookingAccountType
 * @property-read Collection<int, Transaction> $transactions
 *
 * @method static Builder<static>|BookingAccount newModelQuery()
 * @method static Builder<static>|BookingAccount newQuery()
 * @method static Builder<static>|BookingAccount query()
 * @method static Builder<static>|BookingAccount whereCategory($value)
 * @method static Builder<static>|BookingAccount whereSubtype($value)
 * @method static Builder<static>|BookingAccount whereArea($value)
 * @method static Builder<static>|BookingAccount whereNumber($value)
 * @method static Builder<static>|BookingAccount whereLabel($value)
 * @method static Builder<static>|BookingAccount whereCreatedAt($value)
 * @method static Builder<static>|BookingAccount whereId($value)
 * @method static Builder<static>|BookingAccount whereUpdatedAt($value)
 * @method static \Database\Factories\Accounting\BookingAccountFactory factory($count = null, $state = [])
 * @method static Builder<static>|BookingAccount paymentAccounts()
 * @method static Builder<static>|BookingAccount byCategory(\App\Enums\AccountCategory $category)
 * @method static Builder<static>|BookingAccount byArea(\App\Enums\BookingAccountArea $area)
 *
 * @mixin Eloquent
 */
final class BookingAccount extends Model
{
    /** @use HasFactory<BookingAccountFactory> */
    use HasFactory;

    protected $fillable = [
        'label',
        'number',
        'area',
        'category',
        'subtype',
        'booking_account_type_id',
    ];

    protected $casts = [
        'category' => AccountCategory::class,
        'subtype' => AccountSubtype::class,
        'area' => BookingAccountArea::class,
    ];

    // ==================== Relationships ====================

    public function bookingAccountType(): BelongsTo
    {
        return $this->belongsTo(BookingAccountType::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'booking_account_id');
    }

    // ==================== Accessors ====================

    /**
     * Ist dieses Konto ein Zahlungsmittelkonto?
     * Relevant für automatische DATEV-Gegenkonto-Ableitung.
     */
    public function isPaymentAccount(): bool
    {
        return $this->subtype?->isPaymentMedium() ?? false;
    }

    public function isIncomeAccount(): bool
    {
        return $this->category === AccountCategory::Income;
    }

    public function isExpenseAccount(): bool
    {
        return $this->category === AccountCategory::Expense;
    }

    // ==================== Scopes ====================

    public function scopePaymentAccounts(Builder $query): Builder
    {
        return $query->whereIn('subtype', [
            AccountSubtype::Bank->value,
            AccountSubtype::Cash->value,
        ]);
    }

    public function scopeByCategory(Builder $query, AccountCategory $category): Builder
    {
        return $query->where('category', $category->value);
    }

    public function scopeByArea(Builder $query, BookingAccountArea $area): Builder
    {
        return $query->where('area', $area->value);
    }
}

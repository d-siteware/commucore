<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use Database\Factories\Accounting\BookingAccountTypeFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $datev_skr_code
 * @property int $account_length
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, BookingAccount> $bookingAccounts
 * @property-read Collection<int, FiscalYear> $fiscalYears
 */
final class BookingAccountType extends Model
{
    /** @use HasFactory<BookingAccountTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'datev_skr_code',
        'account_length',
    ];

    public function bookingAccounts(): HasMany
    {
        return $this->hasMany(BookingAccount::class);
    }

    public function fiscalYears(): HasMany
    {
        return $this->hasMany(FiscalYear::class);
    }
}

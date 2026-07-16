<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use Database\Factories\Accounting\BoxofficePresetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $booking_account_type_id
 * @property int $booking_account_id
 * @property int $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BookingAccountType|null $bookingAccountType
 * @property-read BookingAccount|null $bookingAccount
 */
final class BoxofficePreset extends Model
{
    /** @use HasFactory<BoxofficePresetFactory> */
    use HasFactory;

    protected $fillable = [
        'booking_account_type_id',
        'booking_account_id',
        'priority',
    ];

    public function bookingAccountType(): BelongsTo
    {
        return $this->belongsTo(BookingAccountType::class);
    }

    public function bookingAccount(): BelongsTo
    {
        return $this->belongsTo(BookingAccount::class);
    }
}

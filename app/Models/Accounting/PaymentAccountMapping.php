<?php

declare(strict_types=1);

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $booking_account_type_id
 * @property string $account_type
 * @property string $booking_account_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read BookingAccountType|null $bookingAccountType
 */
final class PaymentAccountMapping extends Model
{
    protected $fillable = [
        'booking_account_type_id',
        'account_type',
        'booking_account_number',
    ];

    public function bookingAccountType(): BelongsTo
    {
        return $this->belongsTo(BookingAccountType::class);
    }
}

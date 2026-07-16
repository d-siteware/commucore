<?php

declare(strict_types=1);

namespace App\Actions\Accounting;

use App\Models\Accounting\BookingAccount;
use Illuminate\Support\Facades\DB;

final class CreateBookingAccount
{
    /**
     * @param array{
     *     number: string,
     *     label: string,
     *     category: string,
     *     subtype: string|null,
     *     area: string,
     *     booking_account_type_id: int|null,
     * } $data
     */
    public static function create(array $data): BookingAccount
    {
        return DB::transaction(static function () use ($data): BookingAccount {
            return BookingAccount::create([
                'number' => $data['number'],
                'label' => $data['label'],
                'category' => $data['category'],
                'subtype' => $data['subtype'],
                'area' => $data['area'],
                'booking_account_type_id' => $data['booking_account_type_id'],
            ]);
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

use Carbon\Carbon;

enum DateRange: string
{
    case All = 'Alle';
    case Today = 'Heute';
    case Week = 'Woche';
    case Last_7 = 'last7';
    case Last_30 = 'last30';
    case Year = 'DiesesJahr';

    public function label(): string
    {
        return match ($this) {
            self::All => __('app.daterange.all'),
            self::Today => __('app.daterange.today'),
            self::Week => __('app.daterange.week'),
            self::Last_7 => __('app.daterange.last_7'),
            self::Last_30 => __('app.daterange.last_30'),
            self::Year => __('app.daterange.this_year'),
        };
    }

    public function dates(): array
    {
        return match ($this) {
            self::Today => [Carbon::today('Europe/Berlin'), Carbon::now('Europe/Berlin')],
            self::Week => [
                Carbon::today('Europe/Berlin')
                    ->startOfWeek(), Carbon::today('Europe/Berlin')
                    ->endOfWeek()
            ],
            self::Last_7 => [
                Carbon::today('Europe/Berlin')
                    ->subDays(6), Carbon::now('Europe/Berlin')
            ],
            self::Last_30 => [
                Carbon::today('Europe/Berlin')
                    ->subDays(29), Carbon::now('Europe/Berlin')
            ],
            self::Year => [
                Carbon::now('Europe/Berlin')
                    ->startOfYear(), Carbon::now('Europe/Berlin')
            ],
            self::All => [
                Carbon::now('Europe/Berlin')
                    ->startOfCentury(), Carbon::now('Europe/Berlin')
            ],
        };
    }
}

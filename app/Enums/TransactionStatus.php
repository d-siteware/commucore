<?php

declare(strict_types=1);

namespace App\Enums;

use InvalidArgumentException;

enum TransactionStatus: string
{
    case submitted = 'eingereicht';
    case booked = 'gebucht';
    //    case canceled = 'storniert';

    public static function toArray(): array
    {
        return array_column(TransactionStatus::cases(), 'value');
    }

    public static function label(string $value): string
    {
        return match ($value) {
            self::booked->value => __('transaction.status.booked'),
            self::submitted->value => __('transaction.status.submitted'),
            default => throw new InvalidArgumentException("Unknown TransactionStatus: $value"),
        };
    }

    public static function color(string $value): string
    {
        return match ($value) {
            self::submitted->value => 'gray',
            self::booked->value => 'lime',
            default => throw new InvalidArgumentException("Unknown TransactionStatus: $value"),
        };
    }
}

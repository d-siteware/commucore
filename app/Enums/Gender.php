<?php

declare(strict_types=1);

namespace App\Enums;

use InvalidArgumentException;

enum Gender: string
{
    case ma = 'male';
    case fe = 'female';
    case na = 'unknown';

    public static function toArray(): array
    {
        return array_column(Gender::cases(), 'value');
    }

    public static function value(string $value): string
    {

        return match ($value) {
            self::ma->value => __('app.male'),
            self::fe->value => __('app.female'),
            self::na->value => __('app.unknown'),
            default => throw new InvalidArgumentException("Unknown Gender: $value"),

        };

    }
}

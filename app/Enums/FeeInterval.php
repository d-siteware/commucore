<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Contracts\HasLabel;

enum FeeInterval: string implements HasLabel
{
    case MONTHLY = 'monthly';
    case QUARTERLY = 'quarterly';
    case BIANNUAL = 'biannual';
    case YEARLY = 'yearly';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => __('fees.interval.monthly'),
            self::QUARTERLY => __('fees.interval.quarterly'),
            self::BIANNUAL => __('fees.interval.biannual'),
            self::YEARLY => __('fees.interval.yearly'),
            self::CUSTOM => __('fees.interval.custom'),
        };
    }

    /**
     * Returns interval as Carbon-compatible unit + count, or null for CUSTOM.
     * Returns [unit, n] e.g. ['month', 1], ['month', 3], ['month', 6], ['year', 1]
     *
     * @return array{string, int}|null
     */
    public function toCarbonInterval(): ?array
    {
        return match ($this) {
            self::MONTHLY => ['month', 1],
            self::QUARTERLY => ['month', 3],
            self::BIANNUAL => ['month', 6],
            self::YEARLY => ['year', 1],
            self::CUSTOM => null,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $i) => [
            $i->value => $i->label(),
        ])->toArray();
    }
}

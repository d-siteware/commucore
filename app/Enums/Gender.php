<?php

declare(strict_types=1);

namespace App\Enums;

enum Gender: string
{
    case ma = 'male';
    case fe = 'female';
    case na = 'unknown';

    /**
     * Get the translated label for this gender
     */
    public function label(): string
    {
        return match ($this) {
            self::ma => __('app.male'),
            self::fe => __('app.female'),
            self::na => __('app.unknown'),
        };
    }

    /**
     * Get the salutation prefix
     */
    public function salutation(): string
    {
        return match ($this) {
            self::ma => __('app.salutation.mr'),
            self::fe => __('app.salutation.mrs'),
            self::na => __('app.salutation.neutral'),
        };
    }

    /**
     * Check if gender is male
     */
    public function isMale(): bool
    {
        return $this === self::ma;
    }

    /**
     * Check if gender is female
     */
    public function isFemale(): bool
    {
        return $this === self::fe;
    }

    /**
     * Check if gender is unknown/not specified
     */
    public function isUnknown(): bool
    {
        return $this === self::na;
    }

    /**
     * Get all values as array (for dropdowns, filters, etc.)
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all cases with labels (for dropdowns)
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function (self $gender): array {
            return [$gender->value => $gender->label()];
        })->toArray();
    }
}

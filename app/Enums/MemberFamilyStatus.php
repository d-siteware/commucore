<?php

declare(strict_types=1);

namespace App\Enums;

enum MemberFamilyStatus: string implements \App\Enums\Contracts\HasLabel
{
    case SI = 'single';
    case MA = 'married';
    case DI = 'divorced';
    case NN = 'n_a';

    /**
     * Get the translated label for this family status
     */
    public function label(): string
    {
        return match ($this) {
            self::SI => __('members.familystatus.single'),
            self::MA => __('members.familystatus.married'),
            self::DI => __('members.familystatus.divorced'),
            self::NN => __('members.familystatus.n_a'),
        };
    }

    /**
     * Get the color for this family status
     */
    public function color(): string
    {
        return match ($this) {
            self::SI => 'lime',
            self::MA => 'emerald',
            self::DI => 'yellow',
            self::NN => 'zinc',
        };
    }

    /**
     * Check if status is single
     */
    public function isSingle(): bool
    {
        return $this === self::SI;
    }

    /**
     * Check if status is married
     */
    public function isMarried(): bool
    {
        return $this === self::MA;
    }

    /**
     * Check if status is divorced
     */
    public function isDivorced(): bool
    {
        return $this === self::DI;
    }

    /**
     * Check if status is not available/not specified
     */
    public function isNotSpecified(): bool
    {
        return $this === self::NN;
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
        return collect(self::cases())->mapWithKeys(function (self $status) {
            return [$status->value => $status->label()];
        })->toArray();
    }
}

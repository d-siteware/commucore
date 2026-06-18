<?php

declare(strict_types=1);

namespace App\Enums;

use App\Enums\Contracts\HasLabel;
use App\Services\FeeService;

enum MemberFeeType: string implements HasLabel
{
    case FREE = 'free';
    case FULL = 'full';
    case DISC = 'discounted';

    /**
     * Get the translated label for this fee type
     */
    public function label(): string
    {
        return match ($this) {
            self::FREE => __('members.fee-type.free'),
            self::FULL => __('members.fee-type.standard'),
            self::DISC => __('members.fee-type.discounted'),
        };
    }

    /**
     * Get the color for this fee type
     */
    public function color(): string
    {
        return match ($this) {
            self::FREE => 'lime',
            self::FULL => 'emerald',
            self::DISC => 'orange',
        };
    }

    /**
     * Get the fee amount for this type.
     * Delegates to FeeService so amounts come from configurable settings.
     * FeeService falls back to MembershipFee enum constants when no setting exists,
     * so existing instances without DB records are safe.
     */
    public function fee(): int
    {
        return app(FeeService::class)->getAmountForType($this);
    }

    /**
     * Check if fee type is free
     */
    public function isFree(): bool
    {
        return $this === self::FREE;
    }

    /**
     * Check if fee type is full
     */
    public function isFull(): bool
    {
        return $this === self::FULL;
    }

    /**
     * Check if fee type is discounted
     */
    public function isDiscounted(): bool
    {
        return $this === self::DISC;
    }

    /**
     * Check if fee is payable (not free)
     */
    public function isPayable(): bool
    {
        return $this !== self::FREE;
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
        return collect(self::cases())->mapWithKeys(function (self $type): array {
            return [$type->value => $type->label()];
        })->toArray();
    }
}

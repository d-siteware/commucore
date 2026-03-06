<?php

declare(strict_types=1);

namespace App\Enums;

enum MemberFeeType: string implements \App\Enums\Contracts\HasLabel
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
     * Get the fee amount for this type
     */
    public function fee(): int
    {
        return match ($this) {
            self::FREE => MembershipFee::FREE->value,
            self::FULL => MembershipFee::FULL->value,
            self::DISC => MembershipFee::DISCOUNTED->value,
        };
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

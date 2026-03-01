<?php

declare(strict_types=1);

namespace App\Enums;

enum MemberType: string
{
    case AP = 'applicant';
    case ST = 'standard';
    case AD = 'advisor';
    case MD = 'board';

    /**
     * Get the translated label for this member type
     */
    public function label(): string
    {
        return match ($this) {
            self::ST => __('members.type.standard'),
            self::MD => __('members.type.board'),
            self::AD => __('members.type.advisor'),
            self::AP => __('members.type.applicant'),
        };
    }

    /**
     * Get the color for this member type
     */
    public function color(): string
    {
        return match ($this) {
            self::ST => 'lime',
            self::MD => 'emerald',
            self::AD => 'orange',
            self::AP => 'yellow',
        };
    }

    /**
     * Check if member type is applicant
     */
    public function isApplicant(): bool
    {
        return $this === self::AP;
    }

    /**
     * Check if member type is standard
     */
    public function isStandard(): bool
    {
        return $this === self::ST;
    }

    /**
     * Check if member type is advisor
     */
    public function isAdvisor(): bool
    {
        return $this === self::AD;
    }

    /**
     * Check if member type is board member
     */
    public function isBoardMember(): bool
    {
        return $this === self::MD;
    }

    /**
     * Check if member has voting rights
     */
    public function hasVotingRights(): bool
    {
        return in_array($this, [self::ST, self::MD], true);
    }

    /**
     * Check if member is fully accepted
     */
    public function isFullMember(): bool
    {
        return in_array($this, [self::ST, self::MD, self::AD], true);
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
        return collect(self::cases())->mapWithKeys(function (self $type) {
            return [$type->value => $type->label()];
        })->toArray();
    }
}

<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Membership\Member;

enum MembershipFee: int implements \App\Enums\Contracts\HasLabel
{
    /**
     *  Fees in cent
     */
    case FULL = 500;
    case DISCOUNTED = 300;
    case FREE = 0;

    public function label(): string
    {
        return match ($this) {
            self::FULL => __('members.fee.full'),
            self::DISCOUNTED => __('members.fee.discounted'),
            self::FREE => __('members.fee.free'),
        };
    }

    public static function getFee(int $age): int
    {
        return match (true) {
            $age >= 80 => self::FREE->value,
            default => self::FULL->value,
        };
    }

    public static function getFeeFromMember(Member $member): int
    {
        return ($member->is_deducted) ? self::DISCOUNTED->value : self::FULL->value;
    }
}

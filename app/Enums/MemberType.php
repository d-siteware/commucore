<?php

declare(strict_types=1);

namespace App\Enums;

use InvalidArgumentException;

enum MemberType: string
{
    case AP = 'applicant';
    case ST = 'standard';
    case AD = 'advisor';
    case MD = 'board';

    public static function toArray(): array
    {
        return array_column(MemberType::cases(), 'value');
    }

    public static function value(string $value): string
    {
        return match ($value) {
            self::ST->value => __('members.type.standard'),
            self::MD->value => __('members.type.board'),
            self::AD->value => __('members.type.advisor'),
            self::AP->value => __('members.type.applicant'),
            default => throw new InvalidArgumentException("Unknown MemberType: $value"),

        };
    }

    public static function color(string $value): string
    {
        return match ($value) {
            self::ST->value => 'lime',
            self::MD->value => 'emerald',
            self::AD->value => 'orange',
            self::AP->value => 'yellow',
            default => throw new InvalidArgumentException("Unknown MemberType: $value"),

        };
    }
}

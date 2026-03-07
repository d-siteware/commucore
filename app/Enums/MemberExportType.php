<?php

declare(strict_types=1);

namespace App\Enums;

enum MemberExportType: string
{
    case STAMMDATEN = 'stammdaten';
    case MEMBERS_ALL = 'members_all';
    case FULL = 'full';

    public function label(): string
    {
        return match ($this) {
            self::STAMMDATEN => __('members.export.type.stammdaten'),
            self::MEMBERS_ALL => __('members.export.type.members_all'),
            self::FULL => __('members.export.type.full'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::STAMMDATEN => __('members.export.type.stammdaten_desc'),
            self::MEMBERS_ALL => __('members.export.type.members_all_desc'),
            self::FULL => __('members.export.type.full_desc'),
        };
    }
}

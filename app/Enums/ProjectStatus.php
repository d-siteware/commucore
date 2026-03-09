<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatus: string
{
    case Planned = 'planned';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Planned => __('projects.status.planned'),
            self::Active => __('projects.status.active'),
            self::Completed => __('projects.status.completed'),
            self::Cancelled => __('projects.status.cancelled'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Planned => 'gray',
            self::Active => 'green',
            self::Completed => 'indigo',
            self::Cancelled => 'red',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $c) => [$c->value => $c->label()])
            ->toArray();
    }
}

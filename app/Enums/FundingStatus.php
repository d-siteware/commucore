<?php

declare(strict_types=1);

namespace App\Enums;

enum FundingStatus: string
{
    case Applied = 'applied';
    case Approved = 'approved';
    case Active = 'active';
    case Completed = 'completed';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Applied => __('funding.status.applied'),
            self::Approved => __('funding.status.approved'),
            self::Active => __('funding.status.active'),
            self::Completed => __('funding.status.completed'),
            self::Rejected => __('funding.status.rejected'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Applied => 'gray',
            self::Approved => 'blue',
            self::Active => 'green',
            self::Completed => 'indigo',
            self::Rejected => 'red',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $c) => [$c->value => $c->label()])
            ->toArray();
    }
}

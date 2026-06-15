<?php

declare(strict_types=1);

namespace App\Enums;

enum SepaMandateStatus: string implements Contracts\HasLabel
{
    case Pending = 'pending';
    case Active = 'active';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('sepa.mandate.status.pending'),
            self::Active => __('sepa.mandate.status.active'),
            self::Cancelled => __('sepa.mandate.status.cancelled'),
            self::Expired => __('sepa.mandate.status.expired'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'amber',
            self::Active => 'emerald',
            self::Cancelled => 'red',
            self::Expired => 'gray',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $s): array => [$s->value => $s->label()])->toArray();
    }
}

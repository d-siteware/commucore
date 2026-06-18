<?php

declare(strict_types=1);

namespace App\Enums;

enum SepaCollectionAttemptStatus: string implements Contracts\HasLabel
{
    case Submitted = 'submitted';
    case Confirmed = 'confirmed';
    case Returned = 'returned';

    public function label(): string
    {
        return match ($this) {
            self::Submitted => 'Eingereicht',
            self::Confirmed => 'Bestätigt',
            self::Returned => 'Rückläufer',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Submitted => 'amber',
            self::Confirmed => 'emerald',
            self::Returned => 'red',
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

<?php

declare(strict_types=1);

namespace App\Enums;

enum SepaMandateType: string implements Contracts\HasLabel
{
    case Core = 'core';
    case B2b = 'b2b';

    public function label(): string
    {
        return match ($this) {
            self::Core => __('sepa.mandate.type.core'),
            self::B2b => __('sepa.mandate.type.b2b'),
        };
    }

    public function isCore(): bool
    {
        return $this === self::Core;
    }

    public function isB2b(): bool
    {
        return $this === self::B2b;
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $t): array => [$t->value => $t->label()])->toArray();
    }
}

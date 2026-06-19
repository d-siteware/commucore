<?php

declare(strict_types=1);

namespace App\Enums;

enum SepaSequenceType: string implements Contracts\HasLabel
{
    case Frst = 'FRST';
    case Rcur = 'RCUR';

    public function label(): string
    {
        return match ($this) {
            self::Frst => 'Erstmaliger Einzug (FRST)',
            self::Rcur => 'Wiederkehrender Einzug (RCUR)',
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

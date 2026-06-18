<?php

declare(strict_types=1);

namespace App\Enums;

enum SepaReturnReasonCode: string
{
    case AC01 = 'AC01';
    case AC04 = 'AC04';
    case AC06 = 'AC06';
    case AM04 = 'AM04';
    case MD01 = 'MD01';
    case MD06 = 'MD06';
    case MD07 = 'MD07';
    case MS02 = 'MS02';
    case MS03 = 'MS03';
    case Other = 'sonstiges';

    public function label(): string
    {
        return match ($this) {
            self::AC01 => 'AC01 – Konto besteht nicht / ungültige IBAN',
            self::AC04 => 'AC04 – Konto aufgelöst',
            self::AC06 => 'AC06 – Konto gesperrt',
            self::AM04 => 'AM04 – Unzureichende Kontodeckung',
            self::MD01 => 'MD01 – Kein gültiges Mandat vorhanden',
            self::MD06 => 'MD06 – Erstattungsverlangen des Zahlungspflichtigen',
            self::MD07 => 'MD07 – Zahlungspflichtiger verstorben / Verein aufgelöst',
            self::MS02 => 'MS02 – Widerspruch durch den Zahlungspflichtigen',
            self::MS03 => 'MS03 – Sonstiger, von der Bank angegebener Grund',
            self::Other => 'Sonstiger Grund (bitte angeben)',
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c): array => [$c->value => $c->label()])->toArray();
    }
}
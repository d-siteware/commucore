<?php

declare(strict_types=1);

namespace App\Enums;

enum AccountType: string
{
    case cash = 'Barkasse';
    case bank = 'Bankkonto';
    case paypal = 'Digital';

    public static function toArray(): array
    {
        return array_column(AccountType::cases(), 'value');
    }
}

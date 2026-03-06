<?php

namespace App\Enums;

enum AccountSubtype: string
{
    case Bank = 'bank';
    case Cash = 'cash';
    case Receivable = 'receivable';
    case Payable = 'payable';
}

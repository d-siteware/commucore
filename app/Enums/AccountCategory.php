<?php

namespace App\Enums;

enum AccountCategory: string
{
    case Asset = 'asset';
    case Liability = 'liability';
    case Income = 'income';
    case Expense = 'expense';

}

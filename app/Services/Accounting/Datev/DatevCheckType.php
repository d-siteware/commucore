<?php

declare(strict_types=1);

namespace App\Services\Accounting\Datev;

enum DatevCheckType: string
{
    case Error = 'error';
    case Warning = 'warning';
}

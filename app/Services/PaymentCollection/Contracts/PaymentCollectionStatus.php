<?php

declare(strict_types=1);

namespace App\Services\PaymentCollection\Contracts;

enum PaymentCollectionStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Failed = 'failed';
    case Returned = 'returned';
}

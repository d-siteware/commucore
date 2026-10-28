<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

final class EInvoiceImportException extends RuntimeException
{
    public function __construct(
        public readonly string $reason,
        ?Throwable $previous = null,
    ) {
        parent::__construct($reason, previous: $previous);
    }
}

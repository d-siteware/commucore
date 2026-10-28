<?php

declare(strict_types=1);

namespace App\DTOs\Accounting;

use App\Enums\TransactionType;

final readonly class EInvoiceData
{
    public function __construct(
        public string $date,
        public string $label,
        public ?string $reference,
        public ?string $description,
        public string $amountNet,
        public string $amountGross,
        public string $tax,
        public int $vat,
        public TransactionType $type,
        public string $format,
    ) {}
}

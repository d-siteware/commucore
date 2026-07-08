<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionStatus: string
{
    case submitted = 'submitted';
    case booked = 'booked';

    public function label(): string
    {
        return match ($this) {
            self::submitted => __('transaction.status.submitted'),
            self::booked => __('transaction.status.booked'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::submitted => 'olive',
            self::booked => 'lime',
        };
    }

    /**
     * Get all values as array (for dropdowns, filters, etc.)
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all cases with labels (for dropdowns)
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function (self $status): array {
            return [$status->value => $status->label()];
        })->toArray();
    }
}

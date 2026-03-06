<?php

declare(strict_types=1);

namespace App\Enums;

use InvalidArgumentException;

enum TransactionType: string
{
    case Deposit = 'Deposit';
    case Withdrawal = 'Withdrawal';
    case Transfer = 'Transfer';
    case Reversal = 'Reversal';

    /**
     * Instance method
     */
    public function label(): string
    {
        return match ($this) {
            self::Deposit => __('transaction.type.deposit'),
            self::Withdrawal => __('transaction.type.withdrawal'),
            self::Transfer => __('transaction.type.transfer'),
            self::Reversal => __('transaction.type.reversal'),
        };
    }

    /**
     * Instance method
     */
    public function color(): string
    {
        return match ($this) {
            self::Deposit => 'green',
            self::Withdrawal => 'red',
            self::Transfer => 'blue',
            self::Reversal => 'orange',
        };
    }

    /**
     * Instance method
     */
    public function isIncome(): bool
    {
        return match ($this) {
            self::Deposit => true,
            default => false,
        };
    }

    /**
     * Instance method
     */
    public function isExpense(): bool
    {
        return match ($this) {
            self::Withdrawal => true,
            default => false,
        };
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function (self $type): array {
            return [$type->value => $type->label()];
        })->toArray();
    }

    /**
     * Get the calculation multiplier for this transaction type
     * Used for balance calculations: amount * multiplier
     */
    public function multiplier(): int
    {
        return match ($this) {
            self::Deposit => 1,
            self::Withdrawal, self::Reversal => -1,
            self::Transfer => 0, // oder 1, je nach Logik
        };
    }

    public static function calc(string $value): int
    {
        return self::fromLegacyType($value)->multiplier();
    }

    /**
     * Legacy: Map alte deutsche Werte zu neuen Enums
     *
     * @deprecated Nur für Migration, danach entfernen
     */
    public static function fromLegacyType(string $legacyValue): self
    {
        return match ($legacyValue) {
            'Einzahlung' => self::Deposit,
            'Auszahlung' => self::Withdrawal,
            'Überweisung' => self::Transfer,
            'Storno' => self::Reversal,
            default => throw new InvalidArgumentException("Unknown legacy TransactionType: $legacyValue"),
        };
    }
}

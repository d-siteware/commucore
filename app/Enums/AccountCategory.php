<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Buchhalterische Grundkategorie eines Kontos.
 *
 * Entspricht den vier Grundtypen der doppelten Buchführung,
 * deckt aber auch EÜR vollständig ab:
 *
 *   Asset     → Aktiva (Vermögen, Forderungen, Zahlungsmittel)
 *   Liability → Passiva (Fremdkapital, Verbindlichkeiten, Rückstellungen)
 *   Income    → Ertrag  (Einnahmen aller Sphären)
 *   Expense   → Aufwand (Ausgaben aller Sphären)
 *
 */
enum AccountCategory: string
{
    case Asset = 'asset';
    case Liability = 'liability';
    case Income = 'income';
    case Expense = 'expense';

    public function label(): string
    {
        return match ($this) {
            self::Asset => __('account.category.asset'),
            self::Liability => __('account.category.liability'),
            self::Income => __('account.category.income'),
            self::Expense => __('account.category.expense'),
        };
    }

    /**
     * Erhöht ein Konto dieser Kategorie den Überschuss bei Deposit?
     * Relevant für EÜR-Saldo-Berechnung.
     */
    public function isIncomeType(): bool
    {
        return $this === self::Income;
    }

    public function isExpenseType(): bool
    {
        return $this === self::Expense;
    }

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $c) => [$c->value => $c->label()])
            ->toArray();
    }
}

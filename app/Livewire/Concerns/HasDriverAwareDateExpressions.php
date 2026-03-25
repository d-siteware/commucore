<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\DB;

/**
 * Stellt DB-treiber-agnostische Datums-Expressions bereit.
 * Unterstützt SQLite, MySQL/MariaDB und PostgreSQL.
 */
trait HasDriverAwareDateExpressions
{
    private function dbDriver(): string
    {
        return DB::connection()->getDriverName();
    }

    /**
     * SQL-Expression: Datum auf Tag kürzen → 'YYYY-MM-DD'
     * Verwendung: ->selectRaw("{$this->exprDay('entered_at')} as period")
     */
    private function exprDay(string $column): string
    {
        return match ($this->dbDriver()) {
            'sqlite' => "strftime('%Y-%m-%d', {$column})",
            'pgsql'  => "TO_CHAR({$column}::date, 'YYYY-MM-DD')",
            default  => "DATE({$column})",                          // MySQL / MariaDB
        };
    }

    /**
     * SQL-Expression: Datum auf Monat kürzen → 'YYYY-MM'
     */
    private function exprMonth(string $column): string
    {
        return match ($this->dbDriver()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql'  => "TO_CHAR({$column}::date, 'YYYY-MM')",
            default  => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }
}
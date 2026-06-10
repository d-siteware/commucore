<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

/**
 * @property int $id
 * @property string $name
 * @property string|null $label
 * @property bool $active
 * @property string $decimal_separator
 * @property string $thousands_separator
 * @property string $name_order 'first_last' | 'last_first'
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locale whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Locale extends Model
{
    protected $fillable = [
        'active',
        'name',
        'label',
        'decimal_separator',
        'thousands_separator',
        'name_order',
        'currency_symbol',
        'currency_position',
        'date_format',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'bool',
        ];
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // =========================================================================
    // Statische Hilfsmethoden
    // =========================================================================

    public static function getNames(): array
    {
        return static::active()->pluck('name')->toArray();
    }

    public static function getLabel(string $name): string
    {
        $locale = static::where('name', $name)->first();

        return $locale?->label;
    }

    public static function isMultiLanguage(): bool
    {
        return count(static::getNames()) > 1;
    }

    public static function fallback(): self
    {
        return static::where('name', 'de')->first()
            ?? static::active()->first()
            ?? static::first();
    }

    public static function available(): array
    {
        $directories = File::directories(lang_path());

        return collect($directories)
            ->map(fn ($dir): string => basename($dir))
            ->toArray();
    }

    // =========================================================================
    // Instanzmethoden
    // =========================================================================

    public function hasLanguageFiles(): bool
    {
        return File::exists(lang_path($this->name));
    }

    /**
     * Formats a float using this locale's separators.
     * Example (de): 1234.5 → "1.234,50"
     */
    public function formatNumber(float $number, int $decimals = 2): string
    {
        return number_format($number, $decimals, $this->decimal_separator, $this->thousands_separator);
    }

    /**
     * Formats a cent integer using this locale's separators.
     * Example (de): 123456 → "1.234,56"
     */
    public function formatCents(int $cents, int $decimals = 2): string
    {
        return $this->formatNumber($cents / 100, $decimals);
    }

    /**
     * Formats a person's name according to this locale's name_order.
     */
    public function formatName(string $firstName, string $lastName): string
    {
        return $this->name_order === 'last_first'
            ? "{$lastName} {$firstName}"
            : "{$firstName} {$lastName}";
    }

    public function label(): string
    {
        return $this->label ?? $this->name;
    }

    public function description(): string
    {
        $sting = $this->name_order === 'last_first' ? 'Nachname, Vorname' : 'Vorname, Nachname';
        $sting .= ' | ';
        $sting .= $this->decimal_separator === ',' ? '10,23' : '10.23';
        $sting .= ' | ';
        $sting .= $this->thousands_separator === '.' ? '1.000.000' : '1,000,000';

        return $sting;

    }
}

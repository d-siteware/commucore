<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

/**
 * @property int $id
 * @property string $name
 * @property string|null $label
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
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
    protected function casts(): array
    {
        return [
            'active' => 'bool',
        ];
    }

    protected $fillable = [
        'active',
        'name',
        'label',
    ];

    // Statische Methoden für Abwärtskompatibilität mit dem alten Enum
    public static function getNames(): array
    {
        return static::active()->pluck('name')->toArray();
    }

    public static function getLabel(string $name): string
    {
        $locale = static::where('name', $name)->first();

        return $locale->label ?? $name;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    // Prüft ob Sprachdateien existieren
    public function hasLanguageFiles(): bool
    {
        return File::exists(lang_path($this->name));
    }

    // Fallback Locale
    public static function fallback(): self
    {
        return static::where('name', 'de')->first()
            ?? static::active()->first()
            ?? static::first();
    }

    // Alle verfügbaren Locales (auch ohne DB-Eintrag)
    public static function available(): array
    {
        $directories = File::directories(lang_path());

        return collect($directories)
            ->map(fn ($dir): string => basename($dir))
            ->toArray();
    }

    public function formatNumber(float $number, int $decimals = 2): string
    {
        return number_format($number, $decimals, $this->decimal_separator, $this->thousands_separator);
    }

    public function formatName(string $firstName, string $lastName): string
    {
        return $this->name_order === 'last_first'
            ? "{$lastName} {$firstName}"
            : "{$firstName} {$lastName}";
    }
}

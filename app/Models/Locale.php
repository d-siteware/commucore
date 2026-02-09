<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

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
        return $locale?->label ?? $name;
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
            ->map(fn($dir) => basename($dir))
            ->toArray();
    }
}

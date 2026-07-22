<?php

declare(strict_types=1);

namespace App\Models\Funding;

use Database\Factories\Funding\FundingPositionCategoryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property bool $is_system
 * @property string $source 'system' | 'custom'
 * @property int $sort
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, FundingPosition> $fundingPositions
 * @property-read int|null $funding_positions_count
 *
 * @method static FundingPositionCategoryFactory factory($count = null, $state = [])
 * @method static Builder<static>|FundingPositionCategory newModelQuery()
 * @method static Builder<static>|FundingPositionCategory newQuery()
 * @method static Builder<static>|FundingPositionCategory query()
 * @method static Builder<static>|FundingPositionCategory system()
 * @method static Builder<static>|FundingPositionCategory custom()
 * @method static Builder<static>|FundingPositionCategory whereId($value)
 * @method static Builder<static>|FundingPositionCategory whereSlug($value)
 * @method static Builder<static>|FundingPositionCategory whereName($value)
 * @method static Builder<static>|FundingPositionCategory whereIsSystem($value)
 * @method static Builder<static>|FundingPositionCategory whereSource($value)
 * @method static Builder<static>|FundingPositionCategory whereSort($value)
 * @method static Builder<static>|FundingPositionCategory whereCreatedAt($value)
 * @method static Builder<static>|FundingPositionCategory whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
final class FundingPositionCategory extends Model
{
    /** @use HasFactory<FundingPositionCategoryFactory> */
    use HasFactory;

    /**
     * Reservierter Slug-Namensraum für tenant-eigene Kategorien.
     * System-Kategorien verwenden Klartext-Slugs ohne diesen Präfix.
     */
    public const CUSTOM_SLUG_PREFIX = 'custom:';

    protected $guarded = [];

    protected $casts = [
        'is_system' => 'boolean',
        'sort' => 'integer',
    ];

    protected static function booted(): void
    {
        // Guards bewusst über Model-Events (nicht Query Builder – der umgeht sie):
        // System-Kategorien sind read-only, damit ein Reseed/Report sich auf
        // stabile Slugs verlassen kann.
        static::deleting(function (FundingPositionCategory $category): void {
            if ($category->is_system) {
                throw ValidationException::withMessages([
                    'category' => __('fundings.positions.categories.error.system_readonly'),
                ]);
            }
        });

        static::updating(function (FundingPositionCategory $category): void {
            if ($category->is_system && $category->isDirty(['slug', 'source', 'is_system'])) {
                throw ValidationException::withMessages([
                    'category' => __('fundings.positions.categories.error.system_readonly'),
                ]);
            }
        });
    }

    /**
     * Provisorischer System-Default der Kategorie-Taxonomie.
     *
     * ACHTUNG: Dieser Satz ist vorläufig. Er wird final erst mit dem echten,
     * prüffesten Verwendungsnachweis gegen einen realen Zuwendungsbescheid
     * geprüft und festgezogen. Wird von Data-Migration (Bestandsinstanzen)
     * und Seeder (Fresh-Installs) gemeinsam genutzt.
     *
     * @return array<int, array{slug: string, name: string, sort: int}>
     */
    public static function systemDefaults(): array
    {
        return [
            ['slug' => 'personalkosten', 'name' => 'Personalkosten', 'sort' => 10],
            ['slug' => 'honorare', 'name' => 'Honorare', 'sort' => 20],
            ['slug' => 'sachkosten', 'name' => 'Sachkosten', 'sort' => 30],
            ['slug' => 'reisekosten', 'name' => 'Reisekosten', 'sort' => 40],
            ['slug' => 'investitionen', 'name' => 'Investitionen', 'sort' => 50],
            ['slug' => 'oeffentlichkeitsarbeit', 'name' => 'Öffentlichkeitsarbeit', 'sort' => 60],
        ];
    }

    // ==================== Relationships ====================

    public function fundingPositions(): HasMany
    {
        return $this->hasMany(FundingPosition::class);
    }

    // ==================== Scopes ====================

    public function scopeSystem(Builder $query): Builder
    {
        return $query->where('is_system', true);
    }

    public function scopeCustom(Builder $query): Builder
    {
        return $query->where('is_system', false);
    }
}

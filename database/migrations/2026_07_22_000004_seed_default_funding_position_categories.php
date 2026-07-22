<?php

declare(strict_types=1);

use App\Models\Funding\FundingPositionCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data-Migration: Legt die provisorischen System-Kategorien für
     * Förderpositionen auf Bestandsinstanzen an (Fresh-Installs zusätzlich
     * über FundingPositionCategorySeeder).
     *
     * PROVISORISCH: Der Kategoriesatz ist ein vorläufiger Default. Der finale,
     * an einem realen Zuwendungsbescheid geprüfte Satz kommt erst mit dem
     * echten Verwendungsnachweis.
     *
     * Idempotent: firstOrCreate-Semantik auf slug – tenant-seitig umbenannte
     * Kategorien werden nicht überschrieben. Tenant-eigene Kategorien leben
     * im "custom:"-Namensraum und kollidieren nie mit diesen Slugs.
     *
     * down() ist absichtlich ein no-op: bestehende Positions-Zuordnungen
     * würden durch ein Löschen der Kategorien verwaisten.
     */
    public function up(): void
    {
        foreach (FundingPositionCategory::systemDefaults() as $default) {
            $exists = DB::table('funding_position_categories')
                ->where('slug', $default['slug'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('funding_position_categories')->insert([
                'slug' => $default['slug'],
                'name' => $default['name'],
                'is_system' => true,
                'source' => 'system',
                'sort' => $default['sort'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // no-op – siehe Klassen-Docblock
    }
};

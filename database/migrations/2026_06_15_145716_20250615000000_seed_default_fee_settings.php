<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['group' => 'fees', 'key' => 'full_amount',       'value' => '500',    'type' => 'integer'],
            ['group' => 'fees', 'key' => 'discounted_amount', 'value' => '300',    'type' => 'integer'],
            ['group' => 'fees', 'key' => 'interval',          'value' => 'yearly', 'type' => 'string'],
            ['group' => 'fees', 'key' => 'interval_n',        'value' => '1',      'type' => 'integer'],
            ['group' => 'fees', 'key' => 'interval_unit',     'value' => 'y',      'type' => 'string'],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']],
            );
        }
    }

    public function down(): void
    {
        Setting::where('group', 'fees')->delete();
    }
};

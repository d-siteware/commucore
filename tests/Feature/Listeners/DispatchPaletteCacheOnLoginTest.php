<?php

declare(strict_types=1);

use App\Listeners\DispatchPaletteCacheOnLogin;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cache;

test('login wärmt den Command-Palette-Cache synchron', function (): void {
    Cache::flush();

    $user = User::factory()->create();
    Member::factory()->create([
        'name' => 'Palettetest',
        'first_name' => 'Susi',
    ]);

    (new DispatchPaletteCacheOnLogin)->handle(new Login('web', $user, false));

    $members = Cache::tags(['palette', 'members'])->get('palette:members', []);

    expect($members)->not->toBeEmpty();
    expect(collect($members)->pluck('label')->join(' '))->toContain('Palettetest');
});

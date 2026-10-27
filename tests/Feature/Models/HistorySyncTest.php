<?php

declare(strict_types=1);

use App\Jobs\RecordHistory;
use App\Models\History;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('user update writes history synchronously with old and new values', function (): void {
    $user = User::factory()->create(['name' => 'Alt']);
    $this->actingAs($user);

    $user->update(['name' => 'Neu']);

    $history = History::where('historable_type', User::class)
        ->where('historable_id', $user->id)
        ->where('action', 'updated')
        ->latest('id')
        ->first();

    expect($history)->not->toBeNull();
    expect($history->changes)->toBe(json_encode(['old' => ['name' => 'Alt'], 'new' => ['name' => 'Neu']]));
});

test('remember_token-only updates are skipped from history', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $user->forceFill(['remember_token' => 'neuer-token'])->save();

    expect(History::where('action', 'updated')
        ->where('historable_type', User::class)
        ->where('historable_id', $user->id)
        ->exists())->toBeFalse();
});

test('record history with a deleted user falls back to null instead of throwing', function (): void {
    $user = User::factory()->create();
    $userId = $user->id;
    $user->delete();

    RecordHistory::dispatchSync($user, 'deleted', null, $userId);

    $history = History::where('historable_id', $userId)
        ->where('historable_type', User::class)
        ->where('action', 'deleted')
        ->latest('id')
        ->first();

    expect($history)->not->toBeNull();
    expect($history->user_id)->toBeNull();
});

test('history insert failures do not interrupt the primary model update', function (): void {
    $user = User::factory()->create(['name' => 'Before']);
    $eventName = 'eloquent.creating: '.History::class;

    Event::listen($eventName, static function (): void {
        throw new RuntimeException('History storage unavailable');
    });

    try {
        $user->update(['name' => 'After']);
    } finally {
        Event::forget($eventName);
    }

    expect($user->fresh()->name)->toBe('After')
        ->and(History::query()
            ->where('historable_type', User::class)
            ->where('historable_id', $user->id)
            ->where('action', 'updated')
            ->exists())->toBeFalse();
});

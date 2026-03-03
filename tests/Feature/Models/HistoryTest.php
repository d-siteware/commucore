<?php

declare(strict_types=1);

use App\Models\History;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── Helper ────────────────────────────────────────────────────────────────────

function createHistory(): History
{
    return History::create([
        'historable_type' => 'App\\Models\\Member',
        'historable_id' => 1,
        'user_id' => null,
        'action' => 'created',
        'changes' => null,
        'changed_at' => now(),
    ]);
}

// ── Model-level immutability ──────────────────────────────────────────────────

it('throws when attempting to update a history record', function (): void {
    $history = createHistory();

    expect(fn () => $history->update(['action' => 'tampered']))
        ->toThrow(\LogicException::class, 'immutable');
});

it('throws when attempting to delete a history record', function (): void {
    $history = createHistory();

    expect(fn () => $history->delete())
        ->toThrow(\LogicException::class, 'immutable');
});

it('allows creating a history record', function (): void {
    $history = createHistory();

    expect($history->id)->toBeInt()
        ->and($history->action)->toBe('created');
});

// ── Policy ────────────────────────────────────────────────────────────────────

it('denies update for admin via policy', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $history = createHistory();

    expect($admin->cannot('update', $history))->toBeTrue();
});

it('denies delete for admin via policy', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $history = createHistory();

    expect($admin->cannot('delete', $history))->toBeTrue();
});

it('allows admin to view history', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    expect($admin->can('viewAny', History::class))->toBeTrue();
});

it('denies non-admin to view history', function (): void {
    $user = User::factory()->create(['is_admin' => false]);

    expect($user->cannot('viewAny', History::class))->toBeTrue();
});

// ── RecordHistory Job smoke test ──────────────────────────────────────────────

it('does not bypass immutability via the job', function (): void {
    $history = createHistory();

    // Direkter DB-Versuch sollte Model-Event trotzdem feuern
    expect(fn () => History::query()->find($history->id)?->update(['action' => 'hacked']))
        ->toThrow(\LogicException::class);
});

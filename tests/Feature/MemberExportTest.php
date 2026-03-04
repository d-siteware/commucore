<?php

declare(strict_types=1);

use App\Enums\ExportType;
use App\Enums\MemberType;
use App\Models\Membership\Member;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── Policy ────────────────────────────────────────────────────────────────────

it('allows admin to export', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    expect($admin->can('export', Member::class))->toBeTrue();
});

it('allows board member to export', function (): void {
    $user = User::factory()->create(['is_admin' => false]);
    Member::factory()->create([
        'user_id' => $user->id,
        'type' => MemberType::MD->value,
        'left_at' => null,
    ]);
    expect($user->can('export', Member::class))->toBeTrue();
});

it('denies standard member from exporting', function (): void {
    $user = User::factory()->create(['is_admin' => false]);
    Member::factory()->create([
        'user_id' => $user->id,
        'type' => MemberType::ST->value,
    ]);
    expect($user->cannot('export', Member::class))->toBeTrue();
});

// ── CSV Export ────────────────────────────────────────────────────────────────

it('returns CSV download for stammdaten export', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    Member::factory()->count(3)->create(['left_at' => null]);

    $response = $this->actingAs($admin)->get(route('backend.members.export.download', [
        'export_type' => ExportType::STAMMDATEN->value,
    ]));

    $response->assertOk()
        ->assertHeader('Content-Type', 'text/csv; charset=utf-8');
});

it('excludes pseudonymised members by default', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    Member::factory()->create(['pseudonymized_at' => now()]);
    Member::factory()->create(['pseudonymized_at' => null, 'name' => 'Sichtbar']);

    $response = $this->actingAs($admin)->get(route('backend.members.export.download', [
        'export_type' => ExportType::STAMMDATEN->value,
    ]));

    $content = $response->streamedContent();

    expect($content)->toContain('Sichtbar')
        ->and($content)->not->toContain('PSEUDONYMIZED');
});

it('includes pseudonymised members when flag is set', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    Member::factory()->create([
        'pseudonymized_at' => now(),
        'name' => 'PSEUDONYMIZED_1',
    ]);

    $response = $this->actingAs($admin)->get(route('backend.members.export.download', [
        'export_type' => ExportType::STAMMDATEN->value,
        'include_pseudonymized' => '1',
    ]));

    expect($response->streamedContent())->toContain('PSEUDONYMIZED_1');
});

it('filters by member type', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    Member::factory()->create(['type' => MemberType::ST->value, 'name' => 'Standard']);
    Member::factory()->create(['type' => MemberType::MD->value, 'name' => 'Vorstand']);

    $response = $this->actingAs($admin)->get(route('backend.members.export.download', [
        'export_type' => ExportType::STAMMDATEN->value,
        'member_types' => [MemberType::MD->value],
    ]));

    $content = $response->streamedContent();
    expect($content)->toContain('Vorstand')
        ->and($content)->not->toContain('Standard');
});

// ── ZIP Export ────────────────────────────────────────────────────────────────

it('returns ZIP download for full export', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    Member::factory()->count(2)->create();

    $response = $this->actingAs($admin)->get(route('backend.members.export.download', [
        'export_type' => ExportType::FULL->value,
    ]));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/zip');
});

// ── Access control ────────────────────────────────────────────────────────────

it('returns 403 for unauthenticated user', function (): void {
    $this->get(route('backend.members.export.download'))->assertRedirect(route('login'));
});

it('returns 403 for standard member', function (): void {
    $user = User::factory()->create(['is_admin' => false]);
    Member::factory()->create(['user_id' => $user->id, 'type' => MemberType::ST->value]);

    $this->actingAs($user)
        ->get(route('backend.members.export.download', ['export_type' => ExportType::STAMMDATEN->value]))
        ->assertForbidden();
});

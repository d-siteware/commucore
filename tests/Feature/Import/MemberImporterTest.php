<?php

declare(strict_types=1);

use App\Models\Membership\Member;
use App\Services\Import\MemberImportBackup;
use App\Services\Import\MemberImporter;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── MemberImportBackup ────────────────────────────────────────────────────────

it('creates a backup JSON file in local storage', function (): void {
    Storage::fake('local');

    Member::factory()->count(3)->create();

    $path = MemberImportBackup::create(userId: 1);

    Storage::disk('local')->assertExists($path);
});

it('backup JSON contains members and member_role tables', function (): void {
    Storage::fake('local');

    Member::factory()->count(2)->create();

    $path = MemberImportBackup::create(userId: 1);
    $content = json_decode(Storage::disk('local')->get($path), associative: true);

    expect($content)->toHaveKey('tables')
        ->and($content['tables'])->toHaveKey('members')
        ->and($content['tables'])->toHaveKey('member_role')
        ->and($content['tables']['members'])->toHaveCount(2);
});

it('restores members from backup', function (): void {
    Storage::fake('local');

    Member::factory()->count(3)->create();
    $path = MemberImportBackup::create(userId: 1);

    // Alle löschen
    Member::query()->forceDelete();
    expect(Member::count())->toBe(0);

    $restored = MemberImportBackup::restore($path);

    expect($restored)->toBe(3)
        ->and(Member::count())->toBe(3);
});

it('throws when backup file does not exist', function (): void {
    Storage::fake('local');
    MemberImportBackup::restore('imports/nonexistent.json');
})->throws(\RuntimeException::class);

it('rollback is allowed within 24 hours', function (): void {
    Storage::fake('local');
    Member::factory()->create();

    $path = MemberImportBackup::create(userId: 1);

    expect(MemberImportBackup::isRollbackAllowed($path))->toBeTrue();
});

// ── MemberImporter ────────────────────────────────────────────────────────────

it('imports valid rows into the members table', function (): void {
    $rows = [
        ['name' => 'Mustermann', 'first_name' => 'Max', 'email' => 'max@example.com'],
        ['name' => 'Schmidt',    'first_name' => 'Eva', 'email' => 'eva@example.com'],
    ];

    $protocol = MemberImporter::import($rows, userId: 1);

    expect($protocol['imported'])->toBe(2)
        ->and($protocol['skipped'])->toBe(0)
        ->and($protocol['errors'])->toBe([])
        ->and(Member::count())->toBe(2);
});

it('skips duplicate emails', function (): void {
    Member::factory()->create(['email' => 'existing@example.com']);

    $rows = [
        ['name' => 'Neu',       'email' => 'neu@example.com'],
        ['name' => 'Duplikat',  'email' => 'existing@example.com'],
    ];

    $protocol = MemberImporter::import($rows, userId: 1);

    expect($protocol['imported'])->toBe(1)
        ->and($protocol['skipped'])->toBe(1);
});

it('skips duplicate emails within the same import batch', function (): void {
    $rows = [
        ['name' => 'Erster',  'email' => 'same@example.com'],
        ['name' => 'Zweiter', 'email' => 'same@example.com'],
    ];

    $protocol = MemberImporter::import($rows, userId: 1);

    expect($protocol['imported'])->toBe(1)
        ->and($protocol['skipped'])->toBe(1);
});

it('records an error for rows missing the name field', function (): void {
    $rows = [['first_name' => 'Kein Name', 'email' => 'noname@example.com']];

    $protocol = MemberImporter::import($rows, userId: 1);

    expect($protocol['imported'])->toBe(0)
        ->and($protocol['errors'])->toHaveCount(1)
        ->and($protocol['errors'][0]['reason'])->toContain('name');
});

it('falls back to MemberType::ST for unknown type values', function (): void {
    $rows = [['name' => 'Unbekannt', 'type' => 'alien']];

    MemberImporter::import($rows, userId: 1);

    expect(Member::first()->type->value)->toBe('standard');
});

it('returns duration in milliseconds', function (): void {
    $protocol = MemberImporter::import([], userId: 1);

    expect($protocol['duration_ms'])->toBeInt()->toBeGreaterThanOrEqual(0);
});

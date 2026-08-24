<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\Import\MemberImportBackup;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('local');
});

function createBackupFile(string $name = 'backup_2026-08-24_120000_test.json', ?int $ageSeconds = null): string
{
    $path = 'imports/'.$name;
    Storage::disk('local')->put($path, json_encode(['tables' => ['members' => [], 'member_role' => []]]));

    if ($ageSeconds !== null) {
        touch(Storage::disk('local')->path($path), now()->subSeconds($ageSeconds)->timestamp);
    }

    return $path;
}

test('downloadUrl zeigt auf die Backup-Route, nicht auf den Export', function (): void {
    $url = MemberImportBackup::downloadUrl('imports/backup_x.json');

    expect($url)->toContain('/import/backup');
    expect($url)->not->toContain('export');
});

test('admin kann das Backup herunterladen', function (): void {
    $path = createBackupFile();
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get(MemberImportBackup::downloadUrl($path))
        ->assertOk()
        ->assertHeader('content-disposition', 'attachment; filename=commucore_backup_'.now()->format('Y-m-d_His').'.json');
});

test('user ohne Export-Recht bekommt 403', function (): void {
    $path = createBackupFile();
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get(MemberImportBackup::downloadUrl($path))
        ->assertForbidden();
});

test('manipulierter path-Param gibt 404 statt 500', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get('/import/backup?path=manipuliert')
        ->assertNotFound();
});

test('verschlüsselter Pfad außerhalb des Backup-Verzeichnisses gibt 404', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get(route('import.backup-download', ['path' => encrypt('.env')]))
        ->assertNotFound();
});

test('abgelaufenes Backup gibt 410', function (): void {
    $path = createBackupFile(ageSeconds: 90000); // 25h alt
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get(MemberImportBackup::downloadUrl($path))
        ->assertStatus(410);
});

test('pruneExpired löscht nur Backups älter als 24h', function (): void {
    $alt = createBackupFile('backup_alt.json', ageSeconds: 90000);
    $neu = createBackupFile('backup_neu.json');
    $andere = 'imports/kein_backup.txt';
    Storage::disk('local')->put($andere, 'x');

    expect(MemberImportBackup::pruneExpired())->toBe(1);

    expect(Storage::disk('local')->exists($alt))->toBeFalse();
    expect(Storage::disk('local')->exists($neu))->toBeTrue();
    expect(Storage::disk('local')->exists($andere))->toBeTrue();
});

test('prune command läuft erfolgreich', function (): void {
    createBackupFile('backup_cmd.json', ageSeconds: 90000);

    $this->artisan('commucore:prune-import-backups')
        ->expectsOutputToContain('1 alte Import-Backups gelöscht')
        ->assertSuccessful();
});

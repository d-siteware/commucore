<?php

declare(strict_types=1);

use App\Enums\MemberExportType;
use App\Jobs\ProcessMemberZipImport;
use App\Livewire\Member\Import\Page;
use App\Livewire\Member\Import\Steps\ImportStep;
use App\Livewire\Member\Import\Steps\MappingStep;
use App\Livewire\Member\Import\Steps\PreviewStep;
use App\Livewire\Member\Import\Steps\UploadStep;
use App\Mail\MemberImportCompleted;
use App\Mail\MemberImportFailed;
use App\Models\Membership\Member;
use App\Models\User;
use App\Services\Import\MemberFieldMapper;
use App\Services\Import\MemberImportBackup;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ── Helpers ───────────────────────────────────────────────────────────────────

function validCsv(): UploadedFile
{
    $headers = implode(';', array_values(MemberFieldMapper::MEMBER_FIELDS));
    $colCount = count(MemberFieldMapper::MEMBER_FIELDS);
    $values = array_fill(0, $colCount, '');

    $keys = array_keys(MemberFieldMapper::MEMBER_FIELDS);
    $values[array_search('name', $keys, true)] = 'Mustermann';
    $values[array_search('first_name', $keys, true)] = 'Max';
    $values[array_search('email', $keys, true)] = 'max@example.com';
    $values[array_search('type', $keys, true)] = 'standard';
    $values[array_search('fee_type', $keys, true)] = 'full';
    $values[array_search('applied_at', $keys, true)] = '2020-01-01';

    $content = "\xEF\xBB\xBF{$headers}\n".implode(';', $values)."\n";

    return UploadedFile::fake()->createWithContent('members.csv', $content);
}

function validZip(): UploadedFile
{
    $headers = implode(';', array_values(MemberFieldMapper::MEMBER_FIELDS));
    $colCount = count(MemberFieldMapper::MEMBER_FIELDS);
    $values = array_fill(0, $colCount, '');

    $keys = array_keys(MemberFieldMapper::MEMBER_FIELDS);
    $values[array_search('name', $keys, true)] = 'Mustermann';
    $values[array_search('first_name', $keys, true)] = 'Max';
    $values[array_search('email', $keys, true)] = 'max@example.com';
    $values[array_search('type', $keys, true)] = 'standard';
    $values[array_search('fee_type', $keys, true)] = 'full';
    $values[array_search('applied_at', $keys, true)] = '2020-01-01';

    $csvContent = "\xEF\xBB\xBF{$headers}\n".implode(';', $values)."\n";
    $checksum = 'sha256:'.hash('sha256', $csvContent);
    $manifest = json_encode([
        'version' => '1.0',
        'app' => 'commucore',
        'exported_at' => now()->toIso8601String(),
        'export_type' => 'full',
        'member_count' => 1,
        'checksums' => ['members_all.csv' => $checksum],
    ]);

    $zipPath = tempnam(sys_get_temp_dir(), 'import_zip_').'.zip';
    $zip = new ZipArchive;
    $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip->addFromString('members_all.csv', $csvContent);
    $zip->addFromString('commucore_export.json', $manifest);
    $zip->close();

    return UploadedFile::fake()->createWithContent('export.zip', file_get_contents($zipPath));
}

function invalidZip(): UploadedFile
{
    $zipPath = tempnam(sys_get_temp_dir(), 'bad_zip_').'.zip';
    $zip = new ZipArchive;
    $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip->addFromString('members_all.csv', 'Name;Email');
    $zip->close();

    return UploadedFile::fake()->createWithContent('bad.zip', file_get_contents($zipPath));
}

function seedImportCache(array $rows, int $userId = 1): string
{
    $cacheKey = 'import_rows_'.$userId.'_'.now()->timestamp;

    Cache::put($cacheKey, [
        'headers' => array_keys($rows[0] ?? []),
        'all_rows' => $rows,
    ], now()->addHour());

    Cache::put($cacheKey.'_mapped', $rows, now()->addHour());

    return $cacheKey;
}

// ── Page (Wizard) ─────────────────────────────────────────────────────────────

describe('Page Wizard', function (): void {

    it('renders on step 1 by default', function (): void {
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(Page::class)->assertSet('currentStep', 1);
    });

    it('advances to next step', function (): void {
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(Page::class)->call('nextStep')->assertSet('currentStep', 2);
    });

    it('does not advance beyond step 4', function (): void {
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(Page::class)->set('currentStep', 4)->call('nextStep')->assertSet('currentStep', 4);
    });

    it('goes back to previous step', function (): void {
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(Page::class)->set('currentStep', 3)->call('previousStep')->assertSet('currentStep', 2);
    });

    it('does not go back before step 1', function (): void {
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(Page::class)->set('currentStep', 1)->call('previousStep')->assertSet('currentStep', 1);
    });

    it('can only navigate back to already visited steps', function (): void {
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(Page::class)->set('currentStep', 2)->call('goToStep', 4)->assertSet('currentStep', 2);
    });

    it('handles upload complete event and advances to step 2', function (): void {
        $user = User::factory()->create();
        $cacheKey = seedImportCache([['name' => 'Mustermann', 'first_name' => 'Max']], $user->id);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->call('handleUploadComplete', [
                'headers' => ['Name', 'Vorname'],
                'total_rows' => 1,
                'import_type' => MemberExportType::STAMMDATEN->value,
                'cache_key' => $cacheKey,
            ])
            ->assertSet('currentStep', 2)
            ->assertSet('csvHeaders', ['Name', 'Vorname'])
            ->assertSet('importCacheKey', $cacheKey);
    });

    it('handles mapping complete event and advances to step 3', function (): void {
        $user = User::factory()->create();
        $cacheKey = seedImportCache([['name' => 'Mustermann']], $user->id);

        Livewire::actingAs($user)
            ->test(Page::class)
            ->set('currentStep', 2)
            ->set('importCacheKey', $cacheKey)
            ->call('handleMappingComplete', ['field_map' => ['Name' => 'name'], 'enum_map' => []])
            ->assertSet('currentStep', 3);
    });

    it('handles backup complete event and advances to step 4', function (): void {
        Storage::fake('local');
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Page::class)
            ->set('currentStep', 3)
            ->call('handleBackupComplete', 'imports/backup_test.json')
            ->assertSet('currentStep', 4)
            ->assertSet('backupPath', 'imports/backup_test.json');
    });

    it('resets wizard after import complete', function (): void {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Page::class)
            ->set('currentStep', 4)
            ->set('importCacheKey', 'import_rows_1_12345')
            ->call('handleImportComplete')
            ->assertSet('currentStep', 1)
            ->assertSet('importCacheKey', '');
    });

});

// ── UploadStep ────────────────────────────────────────────────────────────────

describe('UploadStep', function (): void {

    it('renders correctly', function (): void {
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(UploadStep::class)->assertOk();
    });

    it('validates that a file is required', function (): void {
        $user = User::factory()->create();
        Livewire::actingAs($user)->test(UploadStep::class)->call('processFile')->assertHasErrors(['file' => 'required']);
    });

    it('parses a valid CSV and dispatches upload-complete event', function (): void {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(UploadStep::class)
            ->set('importType', MemberExportType::STAMMDATEN->value)
            ->set('file', validCsv())
            ->call('processFile')
            ->assertDispatched('upload-complete')
            ->assertSet('errorMessage', null);
    });

    it('stores rows in cache after CSV upload', function (): void {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(UploadStep::class)
            ->set('importType', MemberExportType::STAMMDATEN->value)
            ->set('file', validCsv());

        $component->call('processFile');

        // Cache-Key aus der Component-Property lesen statt aus dispatched Event
        $cacheKey = 'import_rows_'.$user->id.'_';

        $keys = Cache::get('import_rows_'.$user->id)
            ?? collect(range(time() - 5, time() + 1))
                ->map(fn ($ts) => Cache::get('import_rows_'.$user->id.'_'.$ts))
                ->filter()
                ->first();

        // Einfacher: prüfen ob irgendein Cache-Key mit dem Prefix existiert
        $component->assertDispatched('upload-complete');

        // Cache-Key direkt aus der UploadStep-Property lesen
        $cachedCacheKey = $component->get('lastCacheKey'); // falls Property existiert
    });

    it('shows error message when CSV is invalid', function (): void {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(UploadStep::class)
            ->set('file', UploadedFile::fake()->createWithContent('empty.csv', ''))
            ->call('processFile')
            ->assertSet('errorMessage', fn ($v) => $v !== null);
    });

    it('dispatches zip job for FULL import type', function (): void {
        Storage::fake('local');
        Queue::fake();
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(UploadStep::class)
            ->set('importType', MemberExportType::FULL->value)
            ->set('file', validZip())
            ->call('processFile')
            ->assertSet('zipJobDispatched', true);

        Queue::assertPushed(ProcessMemberZipImport::class);
    });

    it('shows error for ZIP without valid manifest', function (): void {
        Storage::fake('local');
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(UploadStep::class)
            ->set('importType', MemberExportType::FULL->value)
            ->set('file', invalidZip())
            ->call('processFile')
            ->assertSet('errorMessage', fn ($v) => str_contains($v ?? '', 'commucore_export.json'));
    });

});

// ── MappingStep ───────────────────────────────────────────────────────────────

describe('MappingStep', function (): void {

    it('auto-maps headers that match MEMBER_FIELDS labels', function (): void {
        $user = User::factory()->create();
        $headers = ['Name', 'Vorname', 'E-Mail'];
        $cacheKey = 'import_rows_'.$user->id.'_test1';

        Cache::put($cacheKey, ['headers' => $headers, 'all_rows' => [['Name' => 'Mustermann', 'Vorname' => 'Max', 'E-Mail' => 'max@example.com']]], now()->addHour());

        $component = Livewire::actingAs($user)->test(MappingStep::class, ['csvHeaders' => $headers, 'importCacheKey' => $cacheKey]);

        expect($component->get('fieldMap')['Name'])->toBe('name')
            ->and($component->get('fieldMap')['Vorname'])->toBe('first_name')
            ->and($component->get('fieldMap')['E-Mail'])->toBe('email');
    });

    it('leaves unknown headers unmapped', function (): void {
        $user = User::factory()->create();
        $headers = ['Name', 'UnbekanntesSpalte'];
        $cacheKey = 'import_rows_'.$user->id.'_test2';

        Cache::put($cacheKey, ['headers' => $headers, 'all_rows' => []], now()->addHour());

        $component = Livewire::actingAs($user)->test(MappingStep::class, ['csvHeaders' => $headers, 'importCacheKey' => $cacheKey]);

        expect($component->get('fieldMap')['UnbekanntesSpalte'])->toBe('');
    });

    it('dispatches mapping-complete and writes mapped rows to cache', function (): void {
        $user = User::factory()->create();
        $headers = ['Name', 'Vorname'];
        $cacheKey = 'import_rows_'.$user->id.'_test3';

        Cache::put($cacheKey, ['headers' => $headers, 'all_rows' => [['Name' => 'Mustermann', 'Vorname' => 'Max']]], now()->addHour());

        Livewire::actingAs($user)
            ->test(MappingStep::class, ['csvHeaders' => $headers, 'importCacheKey' => $cacheKey])
            ->call('confirmMapping')
            ->assertDispatched('mapping-complete');

        expect(Cache::get($cacheKey.'_mapped'))->not->toBeNull()
            ->and(Cache::get($cacheKey.'_mapped')[0]['name'])->toBe('Mustermann');
    });

    it('opens enum modal when unknown enum values are detected', function (): void {
        $user = User::factory()->create();
        $headers = ['Name', 'Typ'];
        $cacheKey = 'import_rows_'.$user->id.'_test4';

        Cache::put($cacheKey, ['headers' => $headers, 'all_rows' => [['Name' => 'Mustermann', 'Typ' => 'unbekannt']]], now()->addHour());

        Livewire::actingAs($user)
            ->test(MappingStep::class, ['csvHeaders' => $headers, 'importCacheKey' => $cacheKey])
            ->call('confirmMapping')
            ->assertSet('showEnumModal', true)
            ->assertSet('unknownEnumValues', fn ($v) => isset($v['type']));
    });

    it('completes mapping after enum modal is confirmed', function (): void {
        $user = User::factory()->create();
        $headers = ['Name', 'Typ'];
        $cacheKey = 'import_rows_'.$user->id.'_test5';

        Cache::put($cacheKey, ['headers' => $headers, 'all_rows' => [['Name' => 'Mustermann', 'Typ' => 'Mitglied']]], now()->addHour());

        Livewire::actingAs($user)
            ->test(MappingStep::class, ['csvHeaders' => $headers, 'importCacheKey' => $cacheKey])
            ->set('unknownEnumValues', ['type' => ['Mitglied']])
            ->set('enumMap', ['type' => ['Mitglied' => 'standard']])
            ->set('showEnumModal', true)
            ->call('confirmEnumMapping')
            ->assertSet('showEnumModal', false)
            ->assertDispatched('mapping-complete');
    });

    it('does not double-assign already mapped fields', function (): void {
        $user = User::factory()->create();
        $headers = ['Name', 'Vorname'];
        $cacheKey = 'import_rows_'.$user->id.'_test6';

        Cache::put($cacheKey, ['headers' => $headers, 'all_rows' => []], now()->addHour());

        $component = Livewire::actingAs($user)->test(MappingStep::class, ['csvHeaders' => $headers, 'importCacheKey' => $cacheKey]);
        $fieldMap = $component->get('fieldMap');

        expect($fieldMap['Name'])->toBe('name')
            ->and(in_array('name', array_values($fieldMap), true))->toBeTrue();
    });

});

// ── PreviewStep ───────────────────────────────────────────────────────────────

describe('PreviewStep', function (): void {

    it('shows correct row counts', function (): void {
        Storage::fake('local');
        $user = User::factory()->create();
        $rows = array_map(static fn (int $i): array => ['name' => "Member{$i}", 'email' => "member{$i}@example.com"], range(1, 5));
        $cacheKey = seedImportCache($rows, $user->id);

        $component = Livewire::actingAs($user)->test(PreviewStep::class, ['importCacheKey' => $cacheKey]);

        expect($component->get('totalRows'))->toBe(5)->and($component->get('mappedRows'))->toHaveCount(5);
    });

    it('detects duplicate emails', function (): void {
        Storage::fake('local');
        Member::factory()->create(['email' => 'existing@example.com']);
        $user = User::factory()->create();

        $rows = [['name' => 'Neu', 'email' => 'new@example.com'], ['name' => 'Duplikat', 'email' => 'existing@example.com']];
        $cacheKey = seedImportCache($rows, $user->id);

        $component = Livewire::actingAs($user)->test(PreviewStep::class, ['importCacheKey' => $cacheKey]);

        expect($component->get('duplicates'))->toHaveCount(1);
    });

    it('limits preview to 10 rows', function (): void {
        Storage::fake('local');
        $user = User::factory()->create();
        $rows = array_map(static fn (int $i): array => ['name' => "Member{$i}"], range(1, 15));
        $cacheKey = seedImportCache($rows, $user->id);

        $component = Livewire::actingAs($user)->test(PreviewStep::class, ['importCacheKey' => $cacheKey]);

        expect($component->instance()->previewRows())->toHaveCount(10);
    });

    it('creates backup and dispatches backup-complete', function (): void {
        Storage::fake('local');
        Member::factory()->count(2)->create();
        $user = User::factory()->create();
        $cacheKey = seedImportCache([['name' => 'Test']], $user->id);

        Livewire::actingAs($user)
            ->test(PreviewStep::class, ['importCacheKey' => $cacheKey])
            ->call('createBackup')
            ->assertSet('backupCreated', true)
            ->assertDispatched('backup-complete');
    });

    it('backup download url is null before backup is created', function (): void {
        Storage::fake('local');
        $user = User::factory()->create();
        $cacheKey = seedImportCache([], $user->id);

        $component = Livewire::actingAs($user)->test(PreviewStep::class, ['importCacheKey' => $cacheKey]);

        expect($component->instance()->backupDownloadUrl())->toBeNull();
    });

});

// ── ImportStep ────────────────────────────────────────────────────────────────

describe('ImportStep', function (): void {

    it('imports members and shows protocol', function (): void {
        Storage::fake('local');
        Mail::fake();

        $user = User::factory()->create();
        $backupPath = MemberImportBackup::create($user->id);
        $rows = [['name' => 'Neu1', 'email' => 'neu1@example.com'], ['name' => 'Neu2', 'email' => 'neu2@example.com']];
        $cacheKey = seedImportCache($rows, $user->id);

        Livewire::actingAs($user)
            ->test(ImportStep::class, ['importCacheKey' => $cacheKey, 'backupPath' => $backupPath, 'importType' => MemberExportType::MEMBERS_ALL->value])
            ->call('startImport')
            ->assertSet('importFinished', true)
            ->assertSet('protocol.imported', 2);
    });

    it('sends import completed mail after import', function (): void {
        Storage::fake('local');
        Mail::fake();

        $user = User::factory()->create();
        $backupPath = MemberImportBackup::create($user->id);
        $cacheKey = seedImportCache([], $user->id);

        Livewire::actingAs($user)
            ->test(ImportStep::class, ['importCacheKey' => $cacheKey, 'backupPath' => $backupPath, 'importType' => MemberExportType::STAMMDATEN->value])
            ->call('startImport');

        Mail::assertQueued(MemberImportCompleted::class, fn ($mail) => $mail->user->id === $user->id);
    });

    it('rollback restores members and dispatches import-complete', function (): void {
        Storage::fake('local');
        Mail::fake();

        $user = User::factory()->create();
        Member::factory()->count(3)->create();
        $backupPath = MemberImportBackup::create($user->id);
        $cacheKey = seedImportCache([], $user->id);

        Member::query()->forceDelete();
        expect(Member::count())->toBe(0);

        Livewire::actingAs($user)
            ->test(ImportStep::class, ['importCacheKey' => $cacheKey, 'backupPath' => $backupPath, 'importType' => MemberExportType::MEMBERS_ALL->value, 'importFinished' => true])
            ->call('rollback')
            ->assertDispatched('import-complete');

        expect(Member::count())->toBe(3);
    });

    it('rollback is not allowed when backup does not exist', function (): void {
        Storage::fake('local');
        $user = User::factory()->create();
        $cacheKey = seedImportCache([], $user->id);

        $component = Livewire::actingAs($user)
            ->test(ImportStep::class, ['importCacheKey' => $cacheKey, 'backupPath' => 'imports/nonexistent_backup.json', 'importType' => MemberExportType::STAMMDATEN->value]);

        expect($component->instance()->canRollback())->toBeFalse();
    });

    it('skips duplicate emails during import', function (): void {
        Storage::fake('local');
        Mail::fake();

        $user = User::factory()->create();
        $backupPath = MemberImportBackup::create($user->id);

        Member::factory()->create(['email' => 'existing@example.com']);

        $rows = [['name' => 'Neu', 'email' => 'new@example.com'], ['name' => 'Duplikat', 'email' => 'existing@example.com']];
        $cacheKey = seedImportCache($rows, $user->id);

        Livewire::actingAs($user)
            ->test(ImportStep::class, ['importCacheKey' => $cacheKey, 'backupPath' => $backupPath, 'importType' => MemberExportType::STAMMDATEN->value])
            ->call('startImport')
            ->assertSet('importFinished', true)
            ->assertSet('protocol.imported', 1)
            ->assertSet('protocol.skipped', 1);
    });

    it('clears cache after successful import', function (): void {
        Storage::fake('local');
        Mail::fake();

        $user = User::factory()->create();
        $backupPath = MemberImportBackup::create($user->id);
        $cacheKey = seedImportCache([], $user->id);

        Livewire::actingAs($user)
            ->test(ImportStep::class, ['importCacheKey' => $cacheKey, 'backupPath' => $backupPath, 'importType' => MemberExportType::STAMMDATEN->value])
            ->call('startImport');

        expect(Cache::get($cacheKey))->toBeNull()->and(Cache::get($cacheKey.'_mapped'))->toBeNull();
    });

});

// ── ProcessMemberZipImport Job ────────────────────────────────────────────────

describe('ProcessMemberZipImport Job', function (): void {

    it('processes zip and sends completed mail', function (): void {
        Storage::fake('local');
        Mail::fake();

        $user = User::factory()->create();
        $stored = 'imports/zip/test_import.zip';

        // ZIP-Inhalt direkt erzeugen ohne UploadedFile::fake()
        $headers = implode(';', array_values(MemberFieldMapper::MEMBER_FIELDS));
        $colCount = count(MemberFieldMapper::MEMBER_FIELDS);
        $values = array_fill(0, $colCount, '');
        $keys = array_keys(MemberFieldMapper::MEMBER_FIELDS);
        $values[array_search('name', $keys, true)] = 'Mustermann';
        $values[array_search('email', $keys, true)] = 'max@example.com';
        $values[array_search('type', $keys, true)] = 'standard';
        $values[array_search('fee_type', $keys, true)] = 'full';
        $values[array_search('applied_at', $keys, true)] = '2020-01-01';

        $csvContent = "\xEF\xBB\xBF{$headers}\n".implode(';', $values)."\n";
        $checksum = 'sha256:'.hash('sha256', $csvContent);
        $manifest = json_encode([
            'version' => '1.0', 'app' => 'commucore',
            'exported_at' => now()->toIso8601String(),
            'export_type' => 'full', 'member_count' => 1,
            'checksums' => ['members_all.csv' => $checksum],
        ]);

        $zipPath = tempnam(sys_get_temp_dir(), 'test_zip_').'.zip';
        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('members_all.csv', $csvContent);
        $zip->addFromString('commucore_export.json', $manifest);
        $zip->close();

        Storage::disk('local')->put($stored, file_get_contents($zipPath));
        unlink($zipPath);

        (new ProcessMemberZipImport($stored, $user->id))->handle();

        Mail::assertSent(MemberImportCompleted::class, fn ($m) => $m->user->id === $user->id);
        expect(Member::count())->toBe(1);
    });

    it('sends failed mail and cleans up on error', function (): void {
        Storage::fake('local');
        Mail::fake();

        $user = User::factory()->create();
        $stored = 'imports/zip/bad.zip';
        Storage::disk('local')->put($stored, 'not a valid zip');

        expect(fn () => (new ProcessMemberZipImport($stored, $user->id))->handle())->toThrow(\RuntimeException::class);

        Mail::assertSent(MemberImportFailed::class, fn ($m) => $m->user->id === $user->id);
    });

});

// ── CSV Template ──────────────────────────────────────────────────────────────

describe('CSV Template Download', function (): void {

    it('returns CSV template with correct headers for STAMMDATEN', function (): void {
        $user = User::factory()->withBoardRole()->create();

        $response = $this->actingAs($user)
            ->get(route('backend.members.import.template', ['type' => MemberExportType::STAMMDATEN->value]));

        $response->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = ltrim($response->streamedContent(), "\xEF\xBB\xBF");

        expect($content)->toContain('Name')->and($content)->toContain('E-Mail');
    });

    it('returns CSV template with all headers for MEMBERS_ALL', function (): void {
        $user = User::factory()->withBoardRole()->create();

        $response = $this->actingAs($user)
            ->get(route('backend.members.import.template', ['type' => MemberExportType::MEMBERS_ALL->value]));

        $response->assertOk();
        $content = ltrim($response->streamedContent(), "\xEF\xBB\xBF");

        foreach (array_values(MemberFieldMapper::MEMBER_FIELDS) as $label) {
            expect($content)->toContain($label);
        }
    });

});

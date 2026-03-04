<?php

declare(strict_types=1);

use App\Services\Import\ZipImportHandler;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// ── Helpers ───────────────────────────────────────────────────────────────────

function makeTestZip(
    bool $withManifest = true,
    bool $correctChecksum = true,
    bool $withCsv = true,
    bool $withDocuments = false,
): UploadedFile {
    $zipPath = tempnam(sys_get_temp_dir(), 'zip_test_').'.zip';
    $zip = new ZipArchive;
    $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    $csvContent = "name;email\nMustermann;max@example.com\n";

    if ($withCsv) {
        $zip->addFromString('members_all.csv', $csvContent);
    }

    if ($withManifest) {
        $checksum = $correctChecksum
            ? 'sha256:'.hash('sha256', $csvContent)
            : 'sha256:wronghash';

        $manifest = json_encode([
            'version' => '1.0',
            'app' => 'commucore',
            'exported_at' => now()->toIso8601String(),
            'export_type' => 'full',
            'member_count' => 1,
            'checksums' => ['members_all.csv' => $checksum],
        ]);

        $zip->addFromString('commucore_export.json', $manifest);
    }

    if ($withDocuments) {
        $zip->addFromString('documents/1_mustermann/antrag.pdf', '%PDF-test-content');
    }

    $zip->close();

    return new UploadedFile($zipPath, 'export.zip', 'application/zip', null, true);
}

// ── Tests ─────────────────────────────────────────────────────────────────────

it('extracts a valid ZIP and returns csv path and manifest', function (): void {
    Storage::fake('local');

    $file = makeTestZip();
    $result = ZipImportHandler::extract($file);

    expect($result['csv_path'])->toBeString()
        ->and(file_exists($result['csv_path']))->toBeTrue()
        ->and($result['manifest']['app'])->toBe('commucore')
        ->and($result['manifest']['member_count'])->toBe(1);
});

it('throws when manifest is missing', function (): void {
    Storage::fake('local');

    $file = makeTestZip(withManifest: false);
    ZipImportHandler::extract($file);
})->throws(\RuntimeException::class, 'commucore_export.json not found');

it('throws when manifest app is not commucore', function (): void {
    Storage::fake('local');

    $zipPath = tempnam(sys_get_temp_dir(), 'zip_test_').'.zip';
    $zip = new ZipArchive;
    $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip->addFromString('members_all.csv', "name\nMustermann");
    $zip->addFromString('commucore_export.json', json_encode(['app' => 'other', 'checksums' => []]));
    $zip->close();

    $file = new UploadedFile($zipPath, 'export.zip', 'application/zip', null, true);
    ZipImportHandler::extract($file);
})->throws(\RuntimeException::class, 'Invalid manifest');

it('throws on checksum mismatch', function (): void {
    Storage::fake('local');

    $file = makeTestZip(correctChecksum: false);
    ZipImportHandler::extract($file);
})->throws(\RuntimeException::class, 'Checksum mismatch');

it('throws when CSV is missing from ZIP', function (): void {
    Storage::fake('local');

    $file = makeTestZip(withCsv: false);
    ZipImportHandler::extract($file);

})->throws(\RuntimeException::class, 'Checksum file not found: members_all.csv');

it('returns empty document map when no documents folder exists', function (): void {
    Storage::fake('local');

    $result = ZipImportHandler::extract(makeTestZip());

    expect($result['document_map'])->toBe([]);
});

it('reads document map from documents folder', function (): void {
    Storage::fake('local');

    $result = ZipImportHandler::extract(makeTestZip(withDocuments: true));

    expect($result['document_map'])->toHaveCount(1)
        ->and($result['document_map'][0]['member_id'])->toBe(1)
        ->and($result['document_map'][0]['files'])->toHaveCount(1);
});

it('copies documents to member storage paths', function (): void {
    Storage::fake('local');

    $result = ZipImportHandler::extract(makeTestZip(withDocuments: true));

    $copyResult = ZipImportHandler::copyDocuments($result['document_map'], 'local');

    expect($copyResult['copied'])->toBe(1)
        ->and($copyResult['missing'])->toBe(0);

    Storage::disk('local')->assertExists('members/1/documents/antrag.pdf');
});

it('cleans up extracted directory after import', function (): void {
    Storage::fake('local');

    $result = ZipImportHandler::extract(makeTestZip());

    expect(is_dir($result['extract_dir']))->toBeTrue();

    ZipImportHandler::cleanup($result['extract_dir']);

    expect(is_dir($result['extract_dir']))->toBeFalse();
});

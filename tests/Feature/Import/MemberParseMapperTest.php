<?php

declare(strict_types=1);

use App\Services\Import\MemberCsvParser;
use App\Services\Import\MemberFieldMapper;
use Illuminate\Http\UploadedFile;

// ── MemberCsvParser ───────────────────────────────────────────────────────────

it('parses a semicolon-delimited CSV correctly', function (): void {
    $csv = "name;first_name;email\nMustermann;Max;max@example.com\n";
    $file = csvUpload($csv);

    $result = MemberCsvParser::parse($file);

    expect($result['headers'])->toBe(['name', 'first_name', 'email'])
        ->and($result['rows'][0])->toBe([
            'name' => 'Mustermann',
            'first_name' => 'Max',
            'email' => 'max@example.com',
        ])
        ->and($result['total_rows'])->toBe(1)
        ->and($result['delimiter'])->toBe(';');
});

it('parses a comma-delimited CSV correctly', function (): void {
    $csv = "name,first_name,email\nMustermann,Max,max@example.com\n";
    $file = csvUpload($csv);

    $result = MemberCsvParser::parse($file);

    expect($result['delimiter'])->toBe(',')
        ->and($result['headers'])->toBe(['name', 'first_name', 'email']);
});

it('strips UTF-8 BOM from CSV', function (): void {
    $csv = "\xEF\xBB\xBFname;email\nMustermann;max@example.com\n";
    $file = csvUpload($csv);

    $result = MemberCsvParser::parse($file);

    expect($result['headers'][0])->toBe('name'); // kein BOM-Zeichen
});

it('skips rows with wrong column count', function (): void {
    $csv = "name;email\nMustermann;max@example.com\nNurEinFeld\n";
    $file = csvUpload($csv);

    $result = MemberCsvParser::parse($file);

    expect($result['total_rows'])->toBe(1);
});

it('limits preview to 10 rows', function (): void {
    $rows = implode("\n", array_map(
        static fn (int $i): string => "Name{$i};name{$i}@example.com",
        range(1, 15),
    ));
    $csv = "name;email\n{$rows}\n";
    $file = csvUpload($csv);

    $result = MemberCsvParser::parse($file);

    expect(count($result['rows']))->toBe(10)
        ->and($result['total_rows'])->toBe(15);
});

it('throws when CSV is empty', function (): void {
    $file = csvUpload('');
    MemberCsvParser::parse($file);
})->throws(\RuntimeException::class, 'CSV file is empty.');

// ── MemberFieldMapper ─────────────────────────────────────────────────────────

it('auto-maps headers that match CommuCore fields exactly', function (): void {
    $result = MemberFieldMapper::analyse(['name', 'email', 'unknown_field']);

    expect($result['auto_mapped'])->toBe(['name' => 'name', 'email' => 'email'])
        ->and($result['unmapped_csv'])->toBe(['unknown_field'])
        ->and($result['needs_manual_mapping'])->toBeTrue();
});

it('detects no manual mapping needed when all headers match', function (): void {
    $result = MemberFieldMapper::analyse(['name', 'first_name', 'email']);

    expect($result['needs_manual_mapping'])->toBeFalse()
        ->and($result['unmapped_csv'])->toBe([]);
});

it('applies field mapping to a row', function (): void {
    $row = ['Nachname' => 'Mustermann', 'Mail' => 'max@example.com'];
    $fieldMap = ['Nachname' => 'name', 'Mail' => 'email'];

    $result = MemberFieldMapper::applyMapping($row, $fieldMap);

    expect($result)->toBe(['name' => 'Mustermann', 'email' => 'max@example.com']);
});

it('detects unknown enum values in rows', function (): void {
    $rows = [['type' => 'unbekannt', 'fee_type' => 'full']];
    $fieldMap = ['type' => 'type', 'fee_type' => 'fee_type'];

    $unknowns = MemberFieldMapper::detectUnknownEnumValues($rows, $fieldMap);

    expect($unknowns)->toHaveKey('type')
        ->and($unknowns['type'])->toContain('unbekannt')
        ->and($unknowns)->not->toHaveKey('fee_type'); // 'full' ist gültig
});

it('applies enum mapping to rows', function (): void {
    $rows = [['type' => 'Mitglied', 'name' => 'Mustermann']];
    $enumMap = ['type' => ['Mitglied' => 'standard']];

    $result = MemberFieldMapper::applyEnumMapping($rows, $enumMap);

    expect($result[0]['type'])->toBe('standard');
});

it('returns enum options for known field', function (): void {
    $options = MemberFieldMapper::enumOptions('type');

    expect($options)->toHaveKey('standard')
        ->and($options)->toHaveKey('board');
});

it('returns empty array for unknown enum field', function (): void {
    expect(MemberFieldMapper::enumOptions('unknown_field'))->toBe([]);
});

// ── Helper ────────────────────────────────────────────────────────────────────

function csvUpload(string $content): UploadedFile
{
    $path = tempnam(sys_get_temp_dir(), 'csv_test_').'.csv';
    file_put_contents($path, $content);

    return new UploadedFile($path, 'members.csv', 'text/csv', null, true);
}

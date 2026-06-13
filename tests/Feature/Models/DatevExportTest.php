<?php

declare(strict_types=1);

use App\Models\Accounting\AccountReport;
use App\Models\Accounting\DatevExport;
use App\Models\User;
use Carbon\Carbon;

test('a datev export can be created', function (): void {
    $report = AccountReport::factory()->create();
    $user = User::factory()->create();

    $export = DatevExport::create([
        'account_report_id' => $report->id,
        'exported_by' => $user->id,
        'filename' => 'export_2025_01.csv',
        'exported_at' => now(),
    ]);

    expect($export)->toBeInstanceOf(DatevExport::class)
        ->and($export->exists)->toBeTrue()
        ->and($export->filename)->toBe('export_2025_01.csv');
});

test('datev export belongs to a report', function (): void {
    $report = AccountReport::factory()->create();
    $user = User::factory()->create();
    $export = DatevExport::create([
        'account_report_id' => $report->id,
        'exported_by' => $user->id,
        'filename' => 'export.csv',
        'exported_at' => now(),
    ]);

    expect($export->report)->toBeInstanceOf(AccountReport::class)
        ->and($export->report->id)->toBe($report->id);
});

test('datev export belongs to a user', function (): void {
    $report = AccountReport::factory()->create();
    $user = User::factory()->create();
    $export = DatevExport::create([
        'account_report_id' => $report->id,
        'exported_by' => $user->id,
        'filename' => 'export.csv',
        'exported_at' => now(),
    ]);

    expect($export->user)->toBeInstanceOf(User::class)
        ->and($export->user->id)->toBe($user->id);
});

test('datev export exported_at is cast to Carbon', function (): void {
    $report = AccountReport::factory()->create();
    $user = User::factory()->create();
    $export = DatevExport::create([
        'account_report_id' => $report->id,
        'exported_by' => $user->id,
        'filename' => 'export.csv',
        'exported_at' => '2025-01-15 10:00:00',
    ]);

    expect($export->exported_at)->toBeInstanceOf(Carbon::class);
});

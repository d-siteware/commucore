<?php

declare(strict_types=1);

use App\Enums\ReportStatus;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\AccountReportAudit;
use App\Models\Accounting\DatevExport;
use App\Models\User;
use Carbon\Carbon;

test('an account report can be created via factory', function (): void {
    $report = AccountReport::factory()->create();

    expect($report)->toBeInstanceOf(AccountReport::class)
        ->and($report->exists)->toBeTrue();
});

test('account report status is cast to enum', function (): void {
    $report = AccountReport::factory()->create(['status' => ReportStatus::draft]);

    expect($report->status)->toBeInstanceOf(ReportStatus::class)
        ->and($report->status->value)->toBe('draft');
});

test('account report period dates are cast to Carbon', function (): void {
    $report = AccountReport::factory()->create();

    expect($report->period_start)->toBeInstanceOf(Carbon::class)
        ->and($report->period_end)->toBeInstanceOf(Carbon::class);
});

test('account report belongs to an account', function (): void {
    $report = AccountReport::factory()->create();

    expect($report->account)->toBeInstanceOf(\App\Models\Accounting\Account::class);
});

test('account report belongs to a user', function (): void {
    $report = AccountReport::factory()->create();

    expect($report->user)->toBeInstanceOf(User::class);
});

test('account report has audits', function (): void {
    $report = AccountReport::factory()->create();
    AccountReportAudit::create([
        'account_report_id' => $report->id,
        'user_id' => User::factory()->create()->id,
    ]);

    expect($report->audits)->toHaveCount(1)
        ->and($report->checkAuditStatus())->toBeTrue();
});

test('account report wasExported returns false without exports', function (): void {
    $report = AccountReport::factory()->create();

    expect($report->wasExported())->toBeFalse();
});

test('account report wasExported returns true after export', function (): void {
    $report = AccountReport::factory()->create();
    DatevExport::create([
        'account_report_id' => $report->id,
        'exported_by' => User::factory()->create()->id,
        'filename' => 'export.csv',
        'exported_at' => now(),
    ]);

    expect($report->wasExported())->toBeTrue();
});

test('setReportStatus sets to rejected when one audit rejects', function (): void {
    $report = AccountReport::factory()->create(['status' => ReportStatus::submitted]);
    $audit = AccountReportAudit::create([
        'account_report_id' => $report->id,
        'user_id' => User::factory()->create()->id,
        'is_approved' => false,
        'approved_at' => now(),
    ]);

    AccountReport::setReportStatus($audit->id);

    expect($report->fresh()->status->value)->toBe('rejected');
});

test('setReportStatus sets to audited when all audits approve', function (): void {
    $report = AccountReport::factory()->create(['status' => ReportStatus::submitted]);
    $audit1 = AccountReportAudit::create([
        'account_report_id' => $report->id,
        'user_id' => User::factory()->create()->id,
        'is_approved' => true,
        'approved_at' => now(),
    ]);
    $audit2 = AccountReportAudit::create([
        'account_report_id' => $report->id,
        'user_id' => User::factory()->create()->id,
        'is_approved' => true,
        'approved_at' => now(),
    ]);

    AccountReport::setReportStatus($audit1->id);

    expect($report->fresh()->status->value)->toBe('audited');
});

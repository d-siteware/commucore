<?php

declare(strict_types=1);

use App\Livewire\Accounting\Report\Audit\Page as AuditPage;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\AccountReportAudit;
use App\Models\Membership\Member;
use App\Models\User;

it('standard user cannot see the audit page', function (): void {

    $member = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id]);

    $user_issuer = $member->user;

    $report = AccountReport::factory()->create();

    $audit = AccountReportAudit::create([
        'account_report_id' => $report->id,
        $user_issuer,
        'user_id' => $user_issuer->id,

    ]);

    $not_designated_user = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user;

    $this->actingAs($not_designated_user);

    $response = $this->get(route('account-report.audit', $audit->id));

    $response->assertStatus(302);

});

it('only designated user can see the audit page', function (): void {

    $user_issuer = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user;

    $report = AccountReport::factory()->create();

    $audit = AccountReportAudit::create([
        'account_report_id' => $report->id,
        'approved_at' => now()->addDays(1),
        'is_approved' => true,
        $user_issuer,
        'user_id' => $user_issuer->id,
    ]);

    $not_designated_user = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id])->user;

    $this->actingAs($user_issuer);

    $response = $this->get(route('account-report.audit', $audit->id));

    $response->assertStatus(200);

});

it('allows the designated auditor to approve their report', function (): void {
    $auditor = User::factory()->create();
    $report = AccountReport::factory()->create();
    $audit = AccountReportAudit::create([
        'account_report_id' => $report->id,
        'user_id' => $auditor->id,
        'is_approved' => false,
        'approved_at' => null,
    ]);

    expect($auditor->can('audit', $audit))->toBeTrue();

    $this->actingAs($auditor);

    Livewire::test(AuditPage::class, ['accountReportAuditId' => $audit->id])
        ->assertSet('form.id', $audit->id)
        ->assertSet('form.user_id', $auditor->id)
        ->call('approveAuditReport')
        ->assertSet('form.is_approved', true)
        ->assertHasNoErrors();

    expect($audit->refresh()->is_approved)->toBeTrue();
});

it('does not let another user approve a designated audit', function (): void {
    $auditor = User::factory()->create();
    $otherUser = User::factory()->create();
    $report = AccountReport::factory()->create();
    $audit = AccountReportAudit::create([
        'account_report_id' => $report->id,
        'user_id' => $auditor->id,
        'is_approved' => false,
        'approved_at' => null,
    ]);

    Livewire::actingAs($otherUser)
        ->test(AuditPage::class, ['accountReportAuditId' => $audit->id])
        ->call('approveAuditReport');

    expect($audit->refresh()->is_approved)->toBeFalse();
});

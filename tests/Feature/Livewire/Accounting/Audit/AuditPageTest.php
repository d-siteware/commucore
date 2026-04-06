<?php

declare(strict_types=1);

use App\Models\Membership\Member;
use App\Models\User;

it('standard user cannot see the audit page', function (): void {

    $member = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id]);

    $user_issuer = $member->user;

    $report = \App\Models\Accounting\AccountReport::factory()->create();

    $audit = \App\Models\Accounting\AccountReportAudit::create([
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

    $report = \App\Models\Accounting\AccountReport::factory()->create();

    $audit = \App\Models\Accounting\AccountReportAudit::create([
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

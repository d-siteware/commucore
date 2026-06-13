<?php

declare(strict_types=1);

use App\Mail\InviteAccountAuditMemberMail;
use App\Models\Accounting\AccountReport;
use App\Models\Accounting\AccountReportAudit;
use App\Models\Membership\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('sends audit invitation mail with correct data', function (): void {
    Mail::fake();

    $user = User::factory()->create();
    $this->actingAs($user);

    $report = AccountReport::factory()->create();
    $member = Member::factory()->create();
    $audit = AccountReportAudit::create([
        'user_id' => $user->id,
        'account_report_id' => $report->id,
    ]);

    Mail::to($member->email)->send(new InviteAccountAuditMemberMail(
        member: $member,
        accountReport: $report,
        accountReportAudit: $audit,
    ));

    Mail::assertQueued(InviteAccountAuditMemberMail::class, function (InviteAccountAuditMemberMail $mail) use ($member, $report, $audit): bool {
        return $mail->member->id === $member->id
            && $mail->accountReport->id === $report->id
            && $mail->accountReportAudit->id === $audit->id
            && $mail->envelope()->subject === __('mails.audit.invitation.subject');
    });
});

it('has correct view and data', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $report = AccountReport::factory()->create();
    $member = Member::factory()->create();
    $audit = AccountReportAudit::create([
        'user_id' => $user->id,
        'account_report_id' => $report->id,
    ]);

    $mail = new InviteAccountAuditMemberMail(
        member: $member,
        accountReport: $report,
        accountReportAudit: $audit,
    );

    $content = $mail->content();

    expect($content->view)->toBe('emails.invite-audit-member')
        ->and($content->with['member']->id)->toBe($member->id)
        ->and($content->with['accountReport']->id)->toBe($report->id);
});

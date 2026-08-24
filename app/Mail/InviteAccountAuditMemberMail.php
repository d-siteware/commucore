<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Accounting\AccountReport;
use App\Models\Accounting\AccountReportAudit;
use App\Models\Membership\Member;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class InviteAccountAuditMemberMail extends Mailable
{
    use SerializesModels;

    public function __construct(public Member $member, public AccountReport $accountReport, public AccountReportAudit $accountReportAudit) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: Auth()->user()->email,
            subject: __('mails.audit.invitation.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invite-audit-member',
            with: [
                'member' => $this->member,
                'accountReport' => $this->accountReport,
                'url' => route('account-report.audit', ['account_report_audit' => $this->accountReportAudit]),
            ],
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Membership\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class AcceptMembershipMail extends Mailable
{
    use Queueable, SerializesModels;

    public Member $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: setting('organization.email'),
            subject: __('mails.acceptance.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.member-acceptance',
            with: ['member' => $this->member]
        );
    }
}

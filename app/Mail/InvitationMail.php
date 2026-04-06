<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Membership\Invitation;
use App\Models\Membership\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class InvitationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Invitation $invitation, public Member $member) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: setting('organization.email'),
            subject: __('mails.invitation.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
            with: ['url' => route('members.register', ['token' => $this->invitation->token])],
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\MailingList;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class ConfirmMailingListSubscription extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public MailingList $mailingList) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: setting('organization.email'),
            subject: __('mails.mailing_list.confirmation_email_subject'),
        );
    }

    public function content(): Content
    {

        return new Content(
            view: 'emails.confirm_mailing_list_subscription',
            with: [
                'mailingList' => $this->mailingList,
                'url' => route('mailing-list.show', $this->mailingList->verification_token),
                'locale' => $this->mailingList->locale,
            ],

        );

    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

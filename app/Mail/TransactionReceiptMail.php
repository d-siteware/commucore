<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class TransactionReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Member $member, public string $filename, public Transaction $transaction) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: setting('organization.email'),
            subject: __('transaction.mail.receipt.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.send-transaction-receipt-mail',
            with: [
                'member' => $this->member,
                'filename' => $this->filename,
                'transaction' => $this->transaction,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if (file_exists($this->filename)) {
            return [
                Attachment::fromPath($this->filename)
                    ->as('Quittung_#Q'.str_pad(''.$this->transaction->id, 6, '0', STR_PAD_LEFT).'.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}

<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Accounting\AccountReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

final class DatevExportMail extends Mailable implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AccountReport $accountReport,
        public string $downloadUrl,
        public string $zipHash,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('accounting.datev.mail.subject', [
                'period' => $this->accountReport->period_start->isoFormat('MMMM Y'),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.datev-export-mail',
            with: [
                'accountReport' => $this->accountReport,
                'url' => $this->downloadUrl,
                'hash' => $this->zipHash,
            ],
        );
    }
}

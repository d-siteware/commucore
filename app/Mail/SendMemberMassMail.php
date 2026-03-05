<?php

declare(strict_types=1);

namespace App\Mail;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class SendMemberMassMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $mail_name,
        public string $mail_subject,
        public string $mail_message,
        public string $mail_locale,
        public ?string $url,
        public ?string $url_label,
        public ?array $mail_attachments = null,
        public ?bool $setPersonalGreeting = true,
        public ?bool $setAttachment = true,
        public ?bool $setLink = true,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: setting('organization.email'),
            subject: $this->mail_subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {

        return new Content(
            view: 'emails.email-to-all-members',
            with: [ // Pass variables here
                'url' => $this->url,
                'url_label' => $this->url_label,
                'mail_name' => $this->mail_name,
                'mail_subject' => $this->mail_subject,
                'mail_message' => $this->mail_message,
                'setPersonalGreeting' => $this->setPersonalGreeting,
                'setAttachment' => $this->setAttachment,
                'setLink' => $this->setLink,
            ]
        );
    }

    /**
     * Attachments for the message.
     */
    public function attachments(): array
    {
        $emailAttachments = [];
        Log::debug('send mail to '.$this->mail_name);
        if ($this->mail_attachments && $this->setAttachment) {
            foreach ($this->mail_attachments as $filePath) {
                // Extract the relative path from the absolute file path
                $relativeFilePath = str_replace(storage_path('app/private').'/', '', $filePath['local']);

                // Check if the relative file path exists in the storage
                if (! Storage::exists($relativeFilePath)) {
                    Log::error('Attachment file missing!', ['filePath' => $relativeFilePath]);
                    throw new Exception('Attachment file missing! Aborting');
                }

                // Get the MIME type of the file
                $mimeType = Storage::mimeType($relativeFilePath); // ?? 'application/octet-stream';

                // Attach the file using the relative path
                $emailAttachments[] = Attachment::fromPath(storage_path("app/private/{$relativeFilePath}"))
                    ->as(basename($filePath['original']))  // Use basename for the file name
                    ->withMime($mimeType);              // Set MIME type for the file

            }
        }

        return $emailAttachments;
    }
}

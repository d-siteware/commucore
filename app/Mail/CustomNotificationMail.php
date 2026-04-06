<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class CustomNotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public string $mailing_subject, public string $mailing_view, public array $data) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: setting('organization.email'),
            subject: $this->mailing_subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->mailing_view,
            with: $this->data,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        if (isset($this->data['event_poster'])) {
            $filePath = 'images/posters/'.$this->data['event_poster'];
            $filename = $this->data['event_poster'];

            if (Storage::disk('public')->exists($filePath)) {
                try {
                    // Read the file content
                    $fileContent = Storage::disk('public')->get($filePath);
                    // Attach the file content
                    $attachments[] = Attachment::fromData(
                        fn () => $fileContent,
                        $filename
                    )->withMime('application/pdf');
                } catch (\Exception $e) {
                    Log::error('Failed to attach file', [
                        'file_path' => $filePath,
                        'filename' => $filename,
                        'error' => $e->getMessage(),
                    ]);
                }
            } else {
                Log::error('Poster file not found in storage', ['file_path' => $filePath]);
            }
        } else {
            Log::info('No poster specified for attachment');
        }

        return $attachments;
    }
}

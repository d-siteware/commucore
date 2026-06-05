<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to the importing user after a member import has completed.
 *
 * @phpstan-type ImportProtocol array{
 *     imported: int,
 *     skipped: int,
 *     errors: array<int, array{row: int, reason: string}>,
 *     duration_ms: int,
 * }
 */
final class MemberImportCompleted extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  ImportProtocol  $protocol
     */
    public function __construct(
        public readonly User $user,
        public readonly array $protocol,
        public readonly string $backupDownloadUrl,
        public readonly string $importedAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: setting('organization.email','helpdesk@commu-core.app'),
            subject: __('members.import.mail.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.members-import-completed',
        );
    }
}

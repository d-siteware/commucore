<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Membership\Member;
use App\Models\Sepa\SepaCollectionAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class SepaReturnDebitNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Member $member,
        private readonly SepaCollectionAttempt $attempt,
        private readonly string $reason,
    ) {}

    /** @return array<int, string> */
    public function via(mixed $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $amount = number_format($this->attempt->amount / 100, 2, ',', '.').'€';

        return (new MailMessage)
            ->from(setting('organization.email'), setting('organization.name', 'Helpdesk'))
            ->subject(__('sepa.notifications.return_debit.subject', ['amount' => $amount]))
            ->line(__('sepa.notifications.return_debit.intro', ['name' => $this->member->fullName(), 'amount' => $amount]))
            ->line(__('sepa.notifications.return_debit.reason', ['reason' => $this->reason]))
            ->line(__('sepa.notifications.return_debit.action'));
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'type' => 'sepa_return_debit',
            'member_id' => $this->member->id,
            'member_name' => $this->member->fullName(),
            'sepa_collection_attempt_id' => $this->attempt->id,
            'transaction_id' => $this->attempt->transaction_id,
            'amount' => $this->attempt->amount,
            'reason' => $this->reason,
            'url' => route('backend.members.show', $this->member->id),
        ];
    }
}

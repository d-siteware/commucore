<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Accounting\Transaction;
use App\Models\Membership\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class SepaReturnDebitNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Member $member,
        private readonly Transaction $transaction,
        private readonly string $reason,
    ) {}

    /** @return array<int, string> */
    public function via(mixed $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $amount = number_format($this->transaction->amount_net / 100, 2, ',', '.');

        return (new MailMessage)
            ->from(setting('organization.email'), setting('organization.name', 'Helpdesk'))
            ->subject(__('sepa.notifications.return_debit.subject', ['amount' => $amount.'€']))
            ->line(__('sepa.notifications.return_debit.intro', [
                'name' => $this->member->fullName(),
                'amount' => $amount.'€',
            ]))
            ->line(__('sepa.notifications.return_debit.reason', ['reason' => $this->reason]))
            ->line(__('sepa.notifications.return_debit.action'));
    }

    /** @return array<string, mixed> */
    public function toArray(mixed $notifiable): array
    {
        return [
            'type' => 'sepa_return_debit',
            'member_id' => $this->member->id,
            'member_name' => $this->member->fullName(),
            'transaction_id' => $this->transaction->id,
            'amount' => $this->transaction->amount_net,
            'reason' => $this->reason,
            'url' => route('backend.members.show', $this->member->id),
        ];
    }
}

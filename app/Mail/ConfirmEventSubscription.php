<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Event\EventSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class ConfirmEventSubscription extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    use SerializesModels;

    /**
     * @var \App\Models\Event\EventSubscription
     */
    public $subscription;

    public $token;

    public function __construct(EventSubscription $subscription, $token)
    {
        $this->subscription = $subscription;
        $this->token = $token;
    }

    public function build(): ConfirmEventSubscription
    {
        return $this->subject('Bitte bestätige deine Anmeldung')
            ->from(setting('organization.email'), 'Körtvélyessy, Daniel')
            ->view('emails.confirm-event-subscription')
            ->with([
                'confirmUrl' => route('events.subscription.confirm', ['eventSubscription' => $this->subscription->id, 'token' => $this->token]),
            ]);
    }
}

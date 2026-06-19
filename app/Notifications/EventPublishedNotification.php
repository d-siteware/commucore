<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Event\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class EventPublishedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Event $event) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Neues Event: '.$this->event->title[app()->getLocale()])
            ->greeting('Hallo '.$notifiable->name.',')
            ->line('Ein neues Event wurde veröffentlicht: **'.$this->event->title[app()->getLocale()].'**')
            ->line('Datum: '.\App\Helpers\DateHelper::formatDate($this->event->event_date))
            ->line($this->event->description)
            ->action('Event ansehen', url('/events/'.$this->event->slug[app()->getLocale()]))
            ->line('Viel Spaß beim Entdecken!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'event_id' => $this->event->id,
            'title' => $this->event->title,
            'message' => 'Ein neues Event wurde veröffentlicht: '.$this->event->title[app()->getLocale()],
            'url' => url('/events/'.$this->event->slug[app()->getLocale()]),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}

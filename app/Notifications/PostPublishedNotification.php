<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Blog\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

final class PostPublishedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Post $post)
    {
        //
    }

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
            ->subject('Neuer Beitrag: '.$this->post->title[app()->getLocale()])
            ->greeting('Hallo '.$notifiable->name.',')
            ->line('Ein neuer Beitrag wurde veröffentlicht: **'.$this->post->title[app()->getLocale()].'**')
            ->line('Datum: '.\App\Helpers\DateHelper::formatDate($this->post->published_at))
            ->line('Auszug: '.Str::limit($this->post->body[app()->getLocale()], 20, ' ... mehr online', true))
            ->action('Beitrag ansehen', url('/posts/'.$this->post->slug[app()->getLocale()]))
            ->line('Viel Spaß beim Entdecken!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'title' => $this->post->title,
            'message' => 'Ein neuer Beitrag wurde veröffentlicht: '.$this->post->title[app()->getLocale()],
            'url' => url('/posts/'.$this->post->slug[app()->getLocale()]),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}

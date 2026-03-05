<?php

declare(strict_types=1);

namespace App\Notifications\Concerns;

use App\Models\User;

trait HasDatabaseChannelForLinkedUsers
{
    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        $channels = ['mail'];

        if ($notifiable instanceof User) {
            $channels[] = 'database';
        }

        return $channels;
    }
}

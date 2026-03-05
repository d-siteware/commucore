<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\View\View;

final class AppLayout extends Component
{
    public string $title = '';

    /** @var Collection<int, DatabaseNotification> */
    public Collection $notifications;

    public function __construct(?string $title = null)
    {
        $this->title = $title
            ? $title.' | '.setting('organization.name')
            : setting('organization.name');

        $user = Auth::user();

        if ($user === null) {
            /** @var Collection<int, DatabaseNotification> $empty */
            $empty = new Collection;
            $this->notifications = $empty;

            return;
        }

        /** @var Collection<int, DatabaseNotification> $unread */
        $unread = $user->unreadNotifications;
        $this->notifications = $unread;
    }

    public function render(): View
    {
        return view('layouts.app');
    }
}

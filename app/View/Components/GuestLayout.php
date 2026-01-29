<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Blog\Post;
use App\Models\Event\Event;
use Illuminate\View\Component;
use Illuminate\View\View;

final class GuestLayout extends Component
{
    public string $title = '';

    public bool $isLoginPage = false;

    public bool $hasEvent = false;

    public ?Event $event = null;

    public ?Post $post = null;

    public string $locale;

    public function __construct(?string $title = null, bool $isLoginPage = false, ?Event $event = null, ?Post $post = null)
    {
        // Default values
        $this->title = $title ?: setting('organization.name');  // Use provided title or fallback to default
        $this->isLoginPage = $isLoginPage;  // Use provided value or default to false
        $this->event = $event;  // Event can be null
        $this->post = $post;
        $this->hasEvent = $event instanceof Event; // Check if event exists
        $this->locale = app()->getLocale(); // Default to 'de' if no locale is set
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return view('layouts.guest', [
            'title' => $this->title,
            'event' => $this->event,
            'post' => $this->post,
            'hasEvent' => $this->hasEvent,
            'locale' => $this->locale,
        ]);
    }
}

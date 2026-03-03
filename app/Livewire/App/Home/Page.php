<?php

declare(strict_types=1);

namespace App\Livewire\App\Home;

use App\Enums\EventStatus;
use App\Models\Blog\Post;
use App\Models\Event\Event;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class Page extends Component
{
    public $events;

    public $events_total;

    public $posts;

    public $posts_count;

    public function mount(): void
    {
        $events = Event::query()
            ->with('venue')
            ->where('status', '=', EventStatus::PUBLISHED)
            ->whereBetween('event_date', [
                Carbon::today('Europe/Berlin'), Carbon::now('Europe/Berlin')
                    ->endOfYear(),
            ])
            ->get();

        $this->events_total = $events->count();
        $this->events = $events->take(2);

        $posts = Post::query()
            ->where('posts.status', EventStatus::PUBLISHED->value)
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->get();

        $this->posts_count = $posts->count();
        $this->posts = $posts->take(1);
    }

    #[Layout('layouts.guest')]
    public function render(): View
    {
        return view('livewire.app.home.page');
    }
}

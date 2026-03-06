<?php

declare(strict_types=1);

namespace App\Livewire\Blog\Post;

use App\Livewire\Forms\Blog\PostForm;
use App\Models\Blog\Post;
use App\Models\Event\Event;
use Flux\Flux;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

final class EventSelector extends Component
{
    public $eventlist = ''; // Bound to the select field

    public $search = '';     // Bound to the search input

    public PostForm $form;

    public function mount(?Post $post = null): void
    {
        if ($post instanceof \App\Models\Blog\Post) {

            $this->form->set($post->id);
        }
    }

    #[Computed]
    public function events(): \Illuminate\Database\Eloquent\Collection
    {
        return Event::query()
            ->select('id', 'name')
            ->where('name', 'like', '%'.$this->search.'%')
            ->limit(5) // Limit results for performance
            ->get();
    }

    public function render(): View
    {
        return view('livewire.blog.post.event-selector');
    }

    public function updatedSearch($value): void {}

    public function connectPostToEvent(): void
    {
        $this->validate([
            'eventlist' => 'required',
        ]);
        if ($this->eventlist != '') {
            $this->form->event_id = $this->eventlist;
            $this->form->update();
            Flux::toast(text: __('post.form.toasts.eventAtachedSuccess'), heading: __('post.form.toasts.heading.success'), variant: 'success');
            $this->dispatch('event-id-updated', $this->form->event_id);
        }
    }
}

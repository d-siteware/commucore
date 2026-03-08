<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Blog\Post\Show;

use App\Livewire\Forms\Blog\PostForm;
use App\Models\Blog\Post;
use Livewire\Component;

final class Page extends Component
{
    public Post $post;

    public PostForm $form;

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->form->set($post->id);
    }

    public function render(): \Illuminate\View\View
    {
        $label = $this->post->label ?? 'neu';

        return view('livewire.blog.post.show.page')->title(__('post.show.title').': '.$label);
    }
}

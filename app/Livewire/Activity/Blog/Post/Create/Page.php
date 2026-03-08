<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Blog\Post\Create;

use Livewire\Component;

final class Page extends Component
{
    public function render(): \Illuminate\View\View
    {
        return view('livewire.blog.post.create.page');
    }
}

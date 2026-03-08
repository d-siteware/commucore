<?php

declare(strict_types=1);

namespace App\Livewire\Activity\Blog\Post\Index;

use App\Enums\EventStatus;
use App\Livewire\Traits\Sortable;
use App\Models\Blog\Post;
use App\Models\Blog\PostType;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

final class Page extends Component
{
    use Sortable, WithPagination;

    public $search;

    public $filteredByStatus;

    public $filteredByType;

    public $locale;

    #[Computed]
    public function posts(): LengthAwarePaginator
    {
        return Post::query()
            ->with('images')
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->tap(fn ($query) => $this->search ? $query->where('title', 'LIKE', '%'.$this->search.'%')
                ->orWhere('label', 'LIKE', '%'.$this->search.'%')
                : $query)
            ->tap(fn ($query) => $this->filteredByStatus ? $query->whereIn('status', $this->filteredByStatus) : $query)
            ->tap(fn ($query) => $this->filteredByType ? $query->whereIn('post_type_id', $this->filteredByType) : $query)
            ->paginate(10);
    }

    public function mount(): void
    {
        $this->search = '';
        $this->filteredByStatus = EventStatus::toArray();
        $this->filteredByType = PostType::all()->pluck('id')->toArray();
        $this->locale = app()->getLocale();
    }

    public function confirmDeletion(int $id): void
    {
        try {
            $post = Post::query()
                ->findOrFail($id);
            //            ->delete();

            $post->delete();

            Flux::toast(__('Der Artikel wurde gelöscht!'));

        } catch (ModelNotFoundException $e) {
            Flux::toast($e->getMessage(), 'error');
        }
    }

    public function render(): \Illuminate\View\View
    {

        return view('livewire.blog.post.index.page');
    }
}

<div>
    <flux:heading size="xl">{{ __('post.show.title') }}</flux:heading>
    <aside class="flex gap-3 my-6">
        <flux:text size="sm">{{ __('post.show.label.created_at') }}: {{ $post->created_at->diffForHumans() }}</flux:text>
        <flux:text size="sm">{{ __('post.show.label.updated_at') }}: {{ $post->updated_at->diffForHumans() }}</flux:text>
    </aside>

    <livewire:activity.blog.post.form :post="$post" />
</div>

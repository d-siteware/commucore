<flux:card size="sm"
           class="p-3! space-y-4"
>

    <flux:badge size="sm"
                color="{{ $post->type->color }}"
    >{{ $post->type->name[$locale] }}</flux:badge>

    <flux:heading size="lg">{{ $post->title[$locale] }}</flux:heading>

    <flux:text>{!! $post->excerpt() !!}</flux:text>

    @if($post->images->count() > 0)
        <flux:subheading>{{ $post->images->count() }} Bilder</flux:subheading>

        <aside class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($post->images->take(2) as $image)
                <figure>
                    <img src="/{{ $image->filename }}"
                         alt="Bild 1"
                         class="aspect-3/2 object-cover rounded-2xl"
                    >

                    <figcaption class="text-sm">
                        {{ $image->caption[$locale] }}
                        @if($image->author)
                            &copy;{{ $image->author }}
                        @endif
                    </figcaption>
                </figure>
            @endforeach
        </aside>

    @endif
    <flux:button size="sm"
                 href="{{route('posts.show',$post->slug[$locale])}}"
                 variant="primary"
    >{{ __('event.show.btn.link_to_post') }}</flux:button>
</flux:card>


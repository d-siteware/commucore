<x-guest-layout :title="$post->title[$locale]" :$post>

    <x-slot name="head">
        @php
            $readingTime = ceil(str_word_count(strip_tags($post->body[$locale] ?? '')) / 200);
        @endphp
            <!-- Open Graph Meta Tags (Facebook, LinkedIn) -->
        <meta property="og:type"
              content="article"
        >
        <meta property="og:title"
              content="{{ Str::limit($post->title[$locale], 70,'..',true) }}"
        >
        <meta property="og:description"
              content="{{ Str::limit(strip_tags($post->body[$locale]), 160,'..',true) }}"
        >
        <meta property="og:url"
              content="{{ url()->current() }}"
        >
        <meta property="og:image"
              content="{{ $post->image_url ?? Vite::asset('resources/images/web-app-manifest-512x512.png') }}"
        >
        <meta property="og:site_name"
              content="{{ setting('organization.name') }}"
        >
        <meta property="og:locale"
              content="{{ app()->getLocale() }}"
        >

        <!-- Twitter Card -->
        <meta name="twitter:card"
              content="summary_large_image"
        >
        <meta name="twitter:title"
              content="{{ Str::limit($post->title[$locale], 70,'..',true) }}"
        >
        <meta name="twitter:description"
              content="{{ Str::limit(strip_tags($post->body[$locale]), 160,'..',true) }}"
        >
        <meta name="twitter:image"
              content="{{ $post->image_url ?? Vite::asset('resources/images/web-app-manifest-512x512.png') }}"
        >

        <!-- article meta tags -->
        <meta name="article:section"
              content="article"
        >
        <meta name="article:published_time"
              content="{{ $post->created_at?->toIso8601String() }}"
        >
        <meta name="article:modified_time"
              content="{{ $post->updated_at?->toIso8601String() }}"
        >
        <meta name="article:reading_time"
              content="{{ $readingTime }} min"
        >

        <!-- Schema.org Structured Data (JSON-LD) -->
        <script type="application/ld+json">
            {
                "@context": "https://schema.org",
                "@type": "Article",
                "headline": "{{ Str::limit($post->title[$locale], 70,'..',true) }}",
            "description": "{{ Str::limit(strip_tags($post->body[$locale]), 160,'..',true) }}",
            "image": "{{ $post->image_url ?? Vite::asset('resources/images/web-app-manifest-512x512.png') }}",
            "author": {
                "@type": "Organization",
                "name": "{{ setting('organization.name') }}"
            },
            "publisher": {
                "@type": "Organization",
                "name": "{{ setting('organization.name') }}",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ Vite::asset('resources/images/web-app-manifest-512x512.png') }}"
                }
            },
            "datePublished": "{{ $post->created_at->toIso8601String() }}",
            "dateModified": "{{ $post->updated_at->toIso8601String() }}",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "{{ url()->current() }}"
            }
        }
        </script>

    </x-slot>

    <flux:heading size="xl"
                  class="py-10"
    ><h1>{{ $post->title[$locale]  }}</h1></flux:heading>

    <section class="{{ $images->count()>0 ? ' grid grid-cols-1 lg:grid-cols-3 gap-6 ' :'' }}">
        <div class="prose dark:prose-invert max-w-none w-full {{ $images->count()>0 ? ' border-r col-span-2  ' :'' }}">
            {!! $post->body[$locale] !!}
        </div>
        {{--        <aside class="px-1 space-y-6">
                    @if($images->count() > 0)
                        <flux:subheading size="xl">Bildergalerie</flux:subheading>
                        @foreach($images as $image)
                            <figure>
                                <img src="/{{ $image->filename }}"
                                     alt="{{ $image->caption[$locale] }}"
                                     loading="lazy"
                                >
                                <flux:text size="sm">&copy; {{ $image->author }}</flux:text>
                                <figcaption>{{ $image->caption[$locale] }}</figcaption>
                            </figure>
                        @endforeach

                    @endif
                </aside>--}}

        <aside class="px-1 space-y-6">
            @if($images->count() > 0)
                <flux:subheading size="xl">Bildergalerie</flux:subheading>
                <div
                    x-data='{
                    lightbox: null,
                    images: @json($images->map(fn($image) => [
                        "filename" => $image->filename,
                        "caption" => $image->caption[$locale],
                        "author" => $image->author
                    ]))
                }'
                    class="space-y-6"
                >
                    @foreach($images as $index => $image)
                        <figure>
                            <img
                                src="/{{ $image->filename }}"
                                alt="{{ $image->caption[$locale] }}"
                                class="cursor-pointer hover:opacity-75 transition-opacity duration-200 w-full h-auto"
                                @click="lightbox = {{ $index }}"
                            >
                            <flux:text size="sm">© {{ $image->author }}</flux:text>
                            <figcaption>{{ $image->caption[$locale] }}</figcaption>
                        </figure>
                    @endforeach

                    <!-- Lightbox -->
                    <div
                        x-show="lightbox !== null"
                        x-cloak
                        x-transition.opacity
                        class="fixed inset-0 bg-black/75 bg-opacity-90 flex items-center justify-center z-50"
                        @click.self="lightbox = null"
                        @keydown.escape="lightbox = null"
                    >
                        <div class="relative max-w-5xl w-full p-2">
                            <template x-if="lightbox !== null">
                                <div>
                                    <img
                                        :src="'/' + images[lightbox].filename"
                                        :alt="images[lightbox].caption"
                                        class="w-full h-auto max-h-[85vh] object-contain"
                                    >
                                    <flux:text size="sm"
                                               class="text-white mt-2"
                                    >
                                        © <span x-text="images[lightbox].author"></span>
                                    </flux:text>
                                    <figcaption class="text-white mt-1"
                                                x-text="images[lightbox].caption"
                                    ></figcaption>
                                </div>
                            </template>
                            <!-- Close Button -->
                            <button
                                @click="lightbox = null"
                                class="absolute top-4 right-4 bg-gray-800 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-gray-600 transition-colors"
                            >
                                ✕
                            </button>
                            <!-- Navigation Arrows -->
                            <button
                                @click="lightbox = (lightbox - 1 + {{ $images->count() }}) % {{ $images->count() }}"
                                class="absolute left-4 top-1/2 -translate-y-1/2 bg-gray-800 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-gray-600 transition-colors"
                            >
                                ←
                            </button>
                            <button
                                @click="lightbox = (lightbox + 1) % {{ $images->count() }}"
                                class="absolute right-4 top-1/2 -translate-y-1/2 bg-gray-800 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-gray-600 transition-colors"
                            >
                                →
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </aside>
    </section>

</x-guest-layout>

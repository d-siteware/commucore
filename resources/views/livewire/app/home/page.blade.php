<div class="grid gap-6 lg:grid-cols-2 lg:gap-8">

    <!-- Event entries -->
    <article class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-hidden focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
    >

        <div class="w-full">
            <div class="pt-3 sm:pt-5 lg:pt-0">
                <h2 class="text-xl font-semibold text-black dark:text-white">{{ __('welcome.events.title') }}</h2>

                <p class="mt-4 text-sm/relaxed">
                    {{ __('welcome.events.listing') }}
                </p>
                @if($events_total>0)
                    @foreach($events as $key => $event)
                        <x-event-item :event="$event"/>
                    @endforeach

                    <flux:button size="sm"
                                 href="{{ route('events.index') }}"
                                 variant="primary"
                                 icon-trailing="arrow-right-circle"
                    >
                        @if ($events_total>1)
                            {{ __('welcome.events.links.label',['num'=>$events_total]) }}
                        @else
                            {{ __('welcome.events.link.label') }}
                        @endif
                    </flux:button>
                @else

                    <flux:text class="mt-14">{{ __('welcome.events.empty.list') }}</flux:text>
                @endif
            </div>
        </div>

    </article>

    <!-- Become a member-->
    <article class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-hidden focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
    >

        <div class="pt-3 sm:pt-5 space-y-2">
            <h2 class="text-xl font-semibold text-black dark:text-white">{{ __('welcome.members.apply.header') }}</h2>

            <p class="mt-4 text-sm/relaxed">{{ __('welcome.members.apply.text') }}</p>
            <flux:button variant="primary"
                         href="{{ route('members.application') }}"
                         icon-trailing="user-plus"
            >{{ __('welcome.members.apply.btn.label') }}</flux:button>
        </div>

    </article>

    <!-- Published post entries -->
    <article class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-hidden focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
    >

        <div class="w-full">
            <div class="pt-3 sm:pt-5 lg:pt-0">
                <h2 class="text-xl font-semibold text-black dark:text-white">{{ __('welcome.articles.title') }}</h2>

                <p class="mt-4 text-sm/relaxed">
                    {{ __('welcome.articles.listing') }}
                </p>


                @if($posts_count>0)
                    @foreach($posts as $key => $post)
                        <x-article-item :post="$post"/>
                    @endforeach

                    <flux:button size="sm"
                                 href="{{ route('posts.index') }}"
                                 variant="primary"
                                 icon-trailing="arrow-right-circle"
                    >
                        {{ __('welcome.articles.link.label',['num'=>$posts_count]) }}
                    </flux:button>
                @else

                    <flux:text class="mt-14">{{ __('welcome.articles.empty.list') }}</flux:text>
                @endif
            </div>
        </div>

    </article>

    <!-- Mission statement -->
    <article class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-hidden focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
    >

        <div class="pt-3 sm:pt-5">
            <h2 class="text-xl font-semibold text-black dark:text-white">{{ organization_slogan() }}</h2>

            <p class="mt-4 text-sm/relaxed">
                {{ organization_description() }}
            </p>
        </div>


    </article>

    <!-- Callout for mailing list subscription-->
    <article class="lg:col-span-2 "
    >
        <livewire:app.global.mailinglist.form/>
    </article>

</div>

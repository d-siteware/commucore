<x-guest-layout :title="__('aboutus.page.title')">

    <div class="overflow-hidden py-24">
        <div class="mx-auto max-w-7xl lg:gap-20 px-6 lg:px-8 ">
            <h1 class="text-lg font-semibold tracking-tight text-pretty ">{{ __('aboutus.page.title') }}</h1>
            <div class="lg:mt-6  prose prose-emerald dark:prose-invert">
                {!! $aboutContent !!}
            </div>
        </div>

        <flux:separator class="my-12"/>

        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-20 px-6 lg:px-8 xl:grid-cols-2">
            <div class="max-w-2xl">
                <h2 class="text-3xl font-semibold tracking-tight text-pretty  sm:text-5xl">{{ __('aboutus.section.numbers.title') }}</h2>
            </div>

            <dl class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">
                <div class="flex flex-col gap-y-2 border-b border-dotted border-gray-200 pb-4">
                    <dt class="text-sm/6 ">{{ __('aboutus.section.numbers.established') }}</dt>
                    <dd class="order-first text-6xl font-semibold tracking-tight">{{ \Illuminate\Support\Carbon::createFromFormat('Y-m-d',setting('organization.registered_date'))->year }}</dd>
                </div>
                <div class="flex flex-col gap-y-2 border-b border-dotted border-gray-200 pb-4">
                    <dt class="text-sm/6 ">{{ __('aboutus.section.numbers.members') }}</dt>
                    <dd class="order-first text-6xl font-semibold tracking-tight"><span>{{ \App\Models\Membership\Member::count()  }}</span></dd>
                </div>
            </dl>
        </div>

        <flux:separator class="my-12"/>

        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-20 px-6 lg:px-8 xl:grid-cols-5">
            <div class="max-w-2xl xl:col-span-2">
                <h2 class="text-3xl font-semibold tracking-tight text-pretty  sm:text-5xl">{{ __('aboutus.section.board.title') }}</h2>
            </div>

            <ul role="list"
                class="divide-y divide-gray-200 xl:col-span-3"
            > @foreach ($team as $member)
                    @foreach ($member->activeRoles as $role)
                        @php
                            $fullName = $member->fullName() ;
                            $leader = $role->pivot;
                        @endphp
                        <li class="flex flex-col gap-10 py-12 first:pt-0 last:pb-0 sm:flex-row">
                            @if($leader->profile_image)
                                <img class="aspect-4/5 w-52 flex-none rounded-2xl object-cover"
                                     src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($leader->profile_image) }}"
                                     alt="{{ $fullName }} - {{ $role->name[$locale] }}"
                                >
                            @else
                                <x-empty_profile_img :name="$member->first_name.' '.$member->name"
                                                     class="aspect-4/5 w-52 flex-none rounded-2xl text-5xl"/>
                            @endif
                            <div class="max-w-xl flex-auto">
                                <h3 class="text-lg/8 font-semibold tracking-tight ">{{ $fullName }}</h3>
                                <p class="text-base/7 ">{{ $role->name[$locale] }}</p>
                                @if($leader->about_me)
                                    <p class="mt-6 text-sm/6 ">{{ $leader->about_me[$locale] }}</p>
                                @endif

                                @if($member->email)
                                    <a href="mailto:{{ $member->email }}"
                                       class="text-gray-400 hover:text-gray-500 mt-6 flex gap-x-6"
                                    >
                                        <flux:icon.envelope-open/>
                                        {{ $member->email }}
                                    </a>
                                @endif


                            </div>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        </div>

        <flux:separator class="my-12"/>
        <div class="mx-auto max-w-7xl lg:gap-20 px-6 lg:px-8 "
             id="statute"
        >

            <p class="text-3xl font-semibold tracking-tight text-pretty sm:text-5xl mb-3 lg:mb-6">{{ __('aboutus.section.statute.title', ['org' => setting('organization.name')]) }}</p>

            <article class="prose prose-emerald dark:prose-invert mb-3 lg:mb-6">{!!  Str::limit($statuteContent, 300) !!}</article>

            <flux:modal.trigger name="show-statute">
                <flux:button variant="primary" >
                    {{ __('aboutus.section.statute.btn.label') }}
                </flux:button>
            </flux:modal.trigger>

            <flux:modal variant="flyout" position="right" name="show-statute"
                        class="prose prose-emerald dark:prose-invert w-full max-w-2xl"
            >
                <article>
                    {!! $statuteContent !!}
                </article>
            </flux:modal>
        </div>
    </div>
</x-guest-layout>

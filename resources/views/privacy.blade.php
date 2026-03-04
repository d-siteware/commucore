<x-guest-layout :title="__('privacy.title')">

    <div class="overflow-hidden py-24">
        <div class="mx-auto max-w-2xl px-6 lg:max-w-7xl lg:px-8">
            <div class="max-w-4xl">
                <p class="text-base/7 font-semibold text-emerald-600">{{ __('aboutus.page.title') }}</p>
                <h1 class="mt-2 text-4xl font-semibold tracking-tight text-pretty  sm:text-5xl">{{ __('privacy.title') }}</h1>
                <p class="mt-6 text-lg/8 text-balance ">{{ __('privacy.p_1') }}</p>
                <p class="mt-6 text-xl/8 text-balance ">{{ setting('organization.name') }}  {{ setting('organization.register_id') }} </p>
                <p class="mt-6 text-lg/8 text-balance ">{{ __('privacy.p_2') }}<br>
                {!!  \App\Models\Membership\Member::leaderBoardHtml($locale) !!}
                </p>
                <flux:separator class="my-12"/>

                @foreach(__('privacy.sections') as $section)
                    <h2 class="mt-6 text-lg/8 text-balance">{{ $section['header'] }}</h2>
                    <p>
                        {{ $section['body'] }}
                        @if(!empty($section['email']))
                            <a href="mailto:{{ setting('organization.email') }}" class="underline text-accent">
                                {{ setting('organization.email') }}
                            </a>
                        @endif
                    </p>
                @endforeach

                <flux:separator class="my-12"/>


            </div>

        </div>



    </div>
</x-guest-layout>

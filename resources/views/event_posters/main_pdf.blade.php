<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Poster: {{$event->title[$locale??'de']}}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }
        body {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
        }

        </style>
        </head>
<body class="font-sans  bg-white relative">

<div class="absolute border-4 border-red-700 inset-2"></div>
<div class="absolute border-4 border-green-700 inset-4"></div>
<div class=" flex flex-col overflow-hidden items-center justify-between  py-10">

    <article class="px-8">
        <header class="flex flex-col justify-center items-center  ">
            <h1 class="text-green-700 text-6xl mb-10">{{ $event->title[$locale??'de'] }}</h1>
        </header>

        <section class="my-3 ">
            {!! $event->excerpt[$locale??'de'] !!}
        </section>


        <section>
            @if($event->timelines()->count()>0)

                <h2 class="text-xl mt-9 mb-4">{{ __('event.timeline.heading',[],$locale??'de') }}</h2>

                <table>
                @foreach($event->timelines()->orderBy('start','desc')->get() as $item)
                   <tr class="">
                       <td class="text-base font-medium text-gray-900 p-3">{{ $item->title_extern[$locale??'de'] }}</td>
                       <td class="text-sm text-gray-700 p-3">
                           @if($item->performer)
                               <p><span>{{ __('event.timeline.performer',[],$locale??'de') }}</span> <strong>{{ $item->performer }}</strong></p>
                           @endif
                               <p><span>{{ __('event.timeline.start',[],$locale??'de') }}</span> <strong>{{ $item->start->format('H:i') }}</strong> <span>{{ __('event.timeline.end',[],$locale??'de') }}</span> <strong>{{ $item->end->format('H:i') }}</strong></p>
                       </td>
                       <td class="text-sm">
                           @if($item->place)
                               <p><span>{{ __('event.timeline.place',[],$locale??'de') }}:</span> <strong>{{ $item->place }}</strong></p>
                           @endif
                       </td>
                   </tr>
                @endforeach
                </table>

            @endif
        </section>

        <aside class="absolute flex bottom-40 left-5 right-5 gap-12 divide-x-2 divide-green-700 bg-green-50 py-4">
            <div class="flex flex-col pl-6">
                <span class="text-5xl text-green-700">{{ \Carbon\Carbon::createFromDate($event->event_date)->locale($locale??'de')->isoFormat('MMMM')  }}</span>
                <span class="text-9xl  px-8 py-3 text-red-700">{{  \Carbon\Carbon::createFromDate($event->event_date)->locale($locale??'de')->isoFormat('Do') }}</span>
            </div>
            <div class="flex flex-col mt-3 w-full pr-6">
                <div class="text-5xl text-red-700 pb-4 border-b-2 border-green-700">{{ $event->start_time->format('H:s') }} - {{ $event->end_time->format('H:s') }}</div>
                <div class=" mt-3 text-green-700">
                    <p class="text-4xl">{{ $event->venue?->name ?? '' }}</p>
                    <p class="text-xl">{{ $event->venue?->address(false) ?? '' }}</p>
                </div>
            </div>
        </aside>

    </article>


    <footer class="absolute bottom-12 left-8 right-8">
        <section class="flex justify-between w-full gap-3 items-start">
            <div class="">
            <img src="{{ app(\App\Services\SettingsService::class)->getLogo() }}"
                         alt="Logo"
                         class="max-w-full max-h-full object-contain"
                    >
            </div>
            <aside class="max-h-36">
                <p>{{ setting('organization.name') }} | {{ setting('organization.register_id') }}</p>
                <p class="text-sm">{!!   \App\Models\Membership\Member::leaderBoardHtml($locale??'de') !!}</p>
            </aside>
            <figure>
                {!! $qrcode !!}
            </figure>
        </section>
    </footer>


</div>


</body>
</html>

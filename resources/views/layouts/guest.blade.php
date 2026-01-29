<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="scroll-smooth"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1"
    >

    <title>{{$title??setting('organization.name')}}</title>

    <link rel="alternate" type="application/rss+xml" title="Events Feed" href="{{ route('api.events.feed') }}">

    <!-- Styles / Scripts -->
    {{--    @turnstileScripts()--}}
    @fluxAppearance
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon"
          href="{{ Vite::asset('resources/images/favicon.ico') }}"
          sizes="48x48"
    >
    <link rel="icon"
          href="{{ Vite::asset('resources/images/logo_commu-core.svg') }}"
          sizes="any"
          type="image/svg+xml"
    >
    <link rel="apple-touch-icon"
          href="{{ Vite::asset('resources/images/web-app-manifest-512x512.png') }}"
    >
    <link rel="apple-touch-icon"
          sizes="180x180"
          href="{{ Vite::asset('resources/images/apple-touch-icon.png') }}"
    />
    <meta name="apple-mobile-web-app-title"
          content="Kolonia"
    />
    <link rel="icon"
          type="image/png"
          href="{{ Vite::asset('resources/images/favicon-96x96.png') }}"
          sizes="96x96"
    />
    <!-- Fonts -->
    <link rel="preconnect"
          href="https://fonts.bunny.net"
    >
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap"
          rel="stylesheet"
    />

    @yield('head')

    @isset($head)
        {{ $head }}
    @endisset

</head>
<body class="font-sans antialiased">
<div class="bg-zinc-50 text-black/50 dark:bg-zinc-900 dark:text-white/50">
    <div class="relative min-h-screen flex flex-col items-center justify-between selection:bg-[#FF2D20] selection:text-white">
        <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl pt-3 lg:pt-6">
           <x-header />
            <flux:navbar class="lg:hidden justify-between mb-3 border-b border-zinc-200 dark:border-zinc-700">
                <a href="/">
                    <x-application-logo class="size-10 ml-2"/>
                </a>
                <flux:heading>{{setting('organization.name')}}</flux:heading>
                <flux:sidebar.toggle class="lg:hidden"
                                     icon="bars-3"
                                     inset="left"
                />
            </flux:navbar>
            {{ $slot }}
        </div>
        @if(isset($event))
            <x-guest-footer :event="$event"/>
        @elseif(isset($post))
            <x-guest-footer :post="$post"/>
        @else
            <x-guest-footer/>
        @endif

        @persist('toast')
        <flux:toast position="top right"/>
        @endpersist
@fluxScripts
</body>
</html>

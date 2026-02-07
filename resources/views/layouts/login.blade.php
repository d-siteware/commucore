<!DOCTYPE html><!-- login temp -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      class="scroll-smooth"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1"
    >
@if(app()->isProduction())
        <meta http-equiv="refresh"
              content="60;url={{ setting('organization.link') }}"
        >
    @else    <meta http-equiv="refresh"
                   content="60;url=https://commucore.test/"
    >
@endif

    <title>{{$title??'Magyar Kolónia Berlin e.V.'}}</title>

    <!-- Fonts -->
    <link rel="preconnect"
          href="https://fonts.bunny.net"
    >
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap"
          rel="stylesheet"
    />

    <!-- Styles / Scripts -->
{{--     @turnstileScripts()--}}
    @fluxAppearance
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-favicon />

    <meta name="apple-mobile-web-app-title"
          content="Kolonia"
    />

    <!-- Styles from branding -->
    <style>
        :root {
            --branding-primary: {{ setting('branding.light.primary') }};
            --branding-secondary: {{ setting('branding.light.secondary') }};
            --branding-brand: {{ setting('branding.light.brand') }};
            --branding-bg: {{ setting('branding.light.bg') }};
            --branding-text: {{ setting('branding.light.text') }};
            --branding-accent: {{ setting('branding.light.accent') }};
            --branding-accent-foreground: {{ setting('branding.light.accent_foreground') }};
            --branding-accent-content: {{ setting('branding.light.accent_content') }};
            --branding-positive: {{ setting('branding.light.positive') }};
            --branding-negative: {{ setting('branding.light.negative') }};
            --branding-storno: {{ setting('branding.light.storno') }};
        }

        .dark {
            --branding-primary: {{ setting('branding.dark.primary') }};
            --branding-secondary: {{ setting('branding.dark.secondary') }};
            --branding-brand: {{ setting('branding.dark.brand') }};
            --branding-bg: {{ setting('branding.dark.bg') }};
            --branding-text: {{ setting('branding.dark.text') }};
            --branding-accent: {{ setting('branding.dark.accent') }};
            --branding-accent-foreground: {{ setting('branding.dark.accent_foreground') }};
            --branding-accent-content: {{ setting('branding.dark.accent_content') }};
            --branding-positive: {{ setting('branding.dark.positive') }};
            --branding-negative: {{ setting('branding.dark.negative') }};
            --branding-storno: {{ setting('branding.dark.storno') }};
        }
    </style>
</head>
<body class="font-sans antialiased">
<div class="bg-zinc-50 text-black/50 dark:bg-black dark:text-white/50">
    <div class="relative min-h-screen flex flex-col items-center justify-between selection:bg-[#FF2D20] selection:text-white">
        <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl pt-3 lg:pt-6">
            <x-header/>
            <flux:navbar class="lg:hidden justify-between mb-3 border-b border-zinc-200 dark:border-zinc-700">
                <a href="/">
                    <x-app-logo class="size-10 ml-2" />
                </a>
                <flux:heading>{{ setting('organization.name') }}</flux:heading>
                <flux:sidebar.toggle class="lg:hidden"
                                     icon="bars-3"
                                     inset="left"
                />
            </flux:navbar>
            {{ $slot }}
        </div>
      <x-guest-footer/>

@persist('toast')
<flux:toast position="top right"/>
@endpersist
@fluxScripts
</body>
</html>

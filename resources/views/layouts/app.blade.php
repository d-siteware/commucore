<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1"
    >
    <meta name="csrf-token"
          content="{{ csrf_token() }}"
    >
    @if(isset($title))
        <title>{{$title . ' @ Magyar Kolónia Berlin e.V.'}}</title>
    @else
        <title>Portal @ Magyar Kolónia Berlin e.V.</title>
    @endif
    <!-- Fonts -->
    <link rel="preconnect"
          href="https://fonts.bunny.net"
    >
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap"
          rel="stylesheet"
    />

    <link rel="icon"
          href="{{ Vite::asset('resources/images/favicon.ico') }}"
          sizes="48x48"
    >
    <link rel="icon"
          href="{{ Vite::asset('resources/images/magyar-kolonia-logo.svg') }}"
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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @fluxAppearance
</head>
<body class="font-sans antialiased min-h-screen bg-white dark:bg-zinc-800">
<flux:sidebar sticky
              collapsible
              class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700"
>
    <flux:sidebar.header>
        <flux:sidebar.brand
            href="/"
                    logo="{{ Vite::asset('resources/images/magyar-kolonia-logo.svg') }}"
                    name="Kolónia Portal"
                    class="px-2"
        />
        <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2"/>
    </flux:sidebar.header>

    <flux:sidebar.nav>
        <flux:sidebar.item icon="home"
                           href="{{ route('dashboard') }}"
                           :current="request()->is('backend/dashboard')"
        >{{ __('nav.dashboard') }}
        </flux:sidebar.item>

        <flux:sidebar.group expandable
                            icon="wrench-screwdriver"
                            heading="{{ __('nav.tools') }}"
                            class="grid"
        >
            <flux:sidebar.item  wire:navigate
                                icon="document-text"
                                   href="{{ route('minutes.index')  }}"
                                   :current="request()->is('backend/minutes')">{{ __('nav.minutes') }}</flux:sidebar.item>
            @can('create', \App\Models\Membership\Member::class)
            <flux:sidebar.item  wire:navigate
                                icon="envelope"
                                href="{{ route('tools.index')  }}"
                                   :current="request()->is('backend/tools')">{{ __('nav.mails') }}</flux:sidebar.item>
            @endcan
            <flux:sidebar.item  wire:navigate
                                icon="photo"
                                href="{{ route('shared-image.index')  }}"
                                   :current="request()->is('backend/shared-images/index')">{{ __('nav.sharedImages') }}</flux:sidebar.item>
        </flux:sidebar.group>

        <flux:sidebar.group expandable
                            icon="user-group"
                            heading="{{ __('nav.members') }}"
                            class="grid"
        >

            <flux:sidebar.item  wire:navigate
                                icon="users"
                                href="{{ route('backend.members.index')  }}"
                                :current="request()->is('backend/members')">{{ __('nav.members.overview') }}</flux:sidebar.item>

            <flux:sidebar.item  wire:navigate
                                icon="identification"
                                href="{{ route('backend.members.roles')  }}"
                                :current="request()->is('backend/members/roles')">{{ __('nav.members.roles') }}</flux:sidebar.item>

        </flux:sidebar.group>

        <flux:sidebar.item wire:navigate
                           icon="newspaper"
                           href="{{ route('backend.posts.index')  }}"
                           :current="request()->is('*posts*')"
        >{{ __('nav.blogs') }}
        </flux:sidebar.item>

        <flux:sidebar.item wire:navigate
                           icon="calendar-days"
                           href="{{ route('backend.events.index') }}"
                           :current="request()->is('*events*')"
        >{{ __('nav.events') }}
        </flux:sidebar.item>


        <flux:sidebar.group expandable
                            icon="banknotes"
                            heading="{{ __('nav.kasse') }}"
                            class="grid"
        >

            <flux:sidebar.item  wire:navigate
                                icon="folder-open"
                                href="{{ route('accounting.index') }}"
                                :current="request()->is('backend/accounting')">{{ __('nav.account.index') }}</flux:sidebar.item>

            <flux:sidebar.item  wire:navigate
                                icon="arrows-right-left"
                                href="{{ route('transaction.index') }}"
                                :current="request()->is('backend/transactions')">{{ __('nav.account.transactions') }}</flux:sidebar.item>

            <flux:sidebar.item  wire:navigate
                                icon="document-currency-euro"
                                href="{{ route('receipts.index') }}"
                                :current="request()->is('backend/receipts')">{{ __('nav.account.receipts') }}</flux:sidebar.item>

            <flux:sidebar.item  wire:navigate
                                icon="document-text"
                                href="{{ route('accounts.report.index') }}"
                                :current="request()->is('backend/account-report')">{{ __('nav.account.reports') }}</flux:sidebar.item>

            @can('create',\App\Models\Accounting\Account::class)
                <flux:sidebar.item  wire:navigate
                                    icon="currency-euro"
                                    href="{{ route('accounts.index') }}"
                                    :current="request()->is('backend/accounts')">{{ __('nav.account.details') }}</flux:sidebar.item>
                @endcan

        </flux:sidebar.group>




    </flux:sidebar.nav>

    <flux:sidebar.spacer/>
    <flux:sidebar.nav>

        @if(Auth::user()->is_admin)
        <flux:sidebar.item icon="information-circle"
                           href="/log-viewer" target="_blank"
        >Logs
        </flux:sidebar.item>
        @endif


    </flux:sidebar.nav>
    <flux:dropdown position="top"
                   align="start"
                   class="max-lg:hidden"
    >
        <flux:sidebar.profile avatar="{{ Auth::user()->profile_photo_url }}"
                              name="{{ Auth::user()->username }}"
        />
        <flux:menu>
            <flux:menu.item wire:navigate
                            icon="user"
                            href="{{ route('profile.show') }}"
            >{{ Auth::user()->first_name. ' '. Auth::user()->name }}</flux:menu.item>
            <flux:menu.item wire:navigate
                            icon="key"
                            href="{{ route('api-tokens.index') }}"
            >{{ __('nav.profile.api') }}</flux:menu.item>
            <livewire:app.global.notifications-menu/>


            <flux:menu.separator/>
            <livewire:app.global.language-switcher/>


            <form method="POST"
                  action="{{ route('logout') }}"
            >
                @csrf
            <flux:menu.item type="submit" icon="arrow-right-start-on-rectangle">{{ __('nav.logout') }}</flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>
<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden"
                         icon="bars-2"
                         inset="left"
    />
    <flux:spacer/>
    <flux:dropdown position="top"
                   align="start"
    >
        <flux:profile avatar="{{ Auth::user()->profile_photo_url }}"/>
        <flux:menu>
            <flux:menu.item wire:navigate
                            icon="user"
                            href="{{ route('profile.show') }}"
            >{{ Auth::user()->first_name. ' '. Auth::user()->name }}</flux:menu.item>
            <flux:menu.item wire:navigate
                            icon="key"
                            href="{{ route('api-tokens.index') }}"
            >{{ __('nav.profile.api') }}</flux:menu.item>
            <livewire:app.global.notifications-menu/>


            <flux:menu.separator/>
            <livewire:app.global.language-switcher/>


            <form method="POST"
                  action="{{ route('logout') }}"
            >
                @csrf
                <flux:menu.item type="submit" icon="arrow-right-start-on-rectangle">{{ __('nav.logout') }}</flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>

<flux:main>
    {{ $slot }}
</flux:main>
@fluxScripts
@persist('toast')
<flux:toast position="top right"/>
@endpersist
</body>
</html>

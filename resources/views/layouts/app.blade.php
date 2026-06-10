@php
    $notifications = Auth::user()?->unreadNotifications ?? new \Illuminate\Database\Eloquent\Collection;
@endphp<!DOCTYPE html>
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
        <title>{{$title . ' @ ' . setting('organization.name')}}</title>
    @else
        <title>Portal @ {{ setting('organization.name') }}</title>
    @endif

    <meta name="apple-mobile-web-app-title"
          content="CommuCore"
    />

    <!-- Fonts -->
    <link rel="preconnect"
          href="https://fonts.bunny.net"
    >
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap"
          rel="stylesheet"
    />

    <x-favicon/>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles from branding -->
    <x-stylesetter/>

    <!-- Styles -->
    @fluxAppearance
</head>
<body
        @class([
          'font-sans antialiased min-h-screen bg-bg dark:bg-bg_dark',
          'pb-10' => config('app.is_demo')
  ])
        x-data="{
        notifications: @js($notifications->values()),
        async markAsRead(id) {
            await fetch('{{ route('notifications.markAsRead', '_id_') }}'.replace('_id_', id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
            });

            if (id === 'all') {
                this.notifications = [];
            } else {
                this.notifications = this.notifications.filter(n => n.id !== id);
            }
        }
    }"
>
<flux:sidebar sticky
              collapsible
              class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700"
>
    <flux:sidebar.header>
        <flux:sidebar.brand
                href="/"
                logo="{{ logo_url() }}"
                name="Portal"
                class="px-2 text-accent text-wrap"
        />
        <div class="absolute top-12 left-14 truncate text-brand in-data-flux-sidebar-collapsed-desktop:hidden">{{ setting('organization.name') }}</div>
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
            <flux:sidebar.item wire:navigate
                               icon="document-text"
                               href="{{ route('minutes.index')  }}"
                               :current="request()->is('backend/minutes') || request()->routeIs('minutes.create') || request()->routeIs('minutes.edit')"
            >{{ __('nav.minutes') }}</flux:sidebar.item>
            @can('create', \App\Models\Membership\Member::class)
                <flux:sidebar.item wire:navigate
                                   icon="envelope"
                                   href="{{ route('backend.tools.mailing')  }}"
                                   :current="request()->routeIs('backend.tools.mailing')"
                >{{ __('nav.mails') }}</flux:sidebar.item>
            @endcan
            <flux:sidebar.item wire:navigate
                               icon="photo"
                               href="{{ route('shared-image.index')  }}"
                               :current="request()->is('backend/shared-images/index')"
            >{{ __('nav.sharedImages') }}</flux:sidebar.item>
        </flux:sidebar.group>

        <flux:sidebar.group expandable
                            icon="user-group"
                            heading="{{ __('nav.members') }}"
                            class="grid"
        >

            <flux:sidebar.item wire:navigate
                               icon="users"
                               href="{{ route('backend.members.index')  }}"
                               :current="request()->is('backend/members') || request()->routeIs('backend.members.import') "
            >{{ __('nav.members.overview') }}</flux:sidebar.item>

            <flux:sidebar.item wire:navigate
                               icon="identification"
                               href="{{ route('backend.members.roles')  }}"
                               :current="request()->is('backend/members/roles')"
            >{{ __('nav.members.roles') }}</flux:sidebar.item>

            <flux:sidebar.item wire:navigate
                               icon="banknotes"
                               href="{{ route('backend.members.fees')  }}"
                               :current="request()->is('backend/members/fees')"
            >{{ __('nav.members.fees') }}</flux:sidebar.item>

        </flux:sidebar.group>

        <flux:sidebar.group expandable
                            icon="megaphone"
                            heading="{{ __('nav.activity') }}"
                            class="grid"
        >
            <flux:sidebar.item wire:navigate
                               icon="calendar-days"
                               href="{{ route('backend.events.index') }}"
                               :current="request()->routeIs('backend.events.*')"
            >{{ __('nav.events') }}
            </flux:sidebar.item>

            <flux:sidebar.item wire:navigate
                               icon="clipboard-document-list"
                               href="{{ route('project.index')  }}"
                               :current="request()->routeIs('project.*')"
            >{{ __('nav.project') }}
            </flux:sidebar.item>

            <flux:sidebar.item wire:navigate
                               icon="newspaper"
                               href="{{ route('backend.posts.index')  }}"
                               :current="request()->routeIs('backend.posts.*')"
            >{{ __('nav.blogs') }}
            </flux:sidebar.item>


        </flux:sidebar.group>

        <flux:sidebar.group expandable
                            icon="banknotes"
                            heading="{{ __('nav.kasse') }}"
                            class="grid"
        >

            <flux:sidebar.item wire:navigate
                               icon="folder-open"
                               href="{{ route('accounting.index') }}"
                               :current="request()->is('backend/accounting')"
            >{{ __('nav.account.index') }}</flux:sidebar.item>

            <flux:sidebar.item wire:navigate
                               icon="arrows-right-left"
                               href="{{ route('transaction.index') }}"
                               :current="request()->is('backend/transactions')"
            >{{ __('nav.account.transactions') }}</flux:sidebar.item>

            <flux:sidebar.item wire:navigate
                               icon="document-currency-euro"
                               href="{{ route('receipts.index') }}"
                               :current="request()->is('backend/receipts')"
            >{{ __('nav.account.receipts') }}</flux:sidebar.item>

            <flux:sidebar.item wire:navigate
                               icon="document-text"
                               href="{{ route('accounts.report.index') }}"
                               :current="request()->is('backend/account-report')"
            >{{ __('nav.account.reports') }}</flux:sidebar.item>

            @can('create',\App\Models\Accounting\Account::class)

                <flux:sidebar.item wire:navigate
                                   icon="currency-euro"
                                   href="{{ route('accounts.index') }}"
                                   :current="request()->is('backend/accounts')"
                >{{ __('nav.account.details') }}</flux:sidebar.item>

                <flux:sidebar.item wire:navigate
                                   icon="calendar"
                                   href="{{ route('fiscal-years.index') }}"
                                   :current="request()->is('backend/fiscal-years') || request()->routeIs('fiscal-years.close') "
                >{{ __('fiscal_year.title') }}</flux:sidebar.item>
            @endcan

            <flux:navlist.item icon="queue-list"
                               href="{{ route('funding.index') }}"
                               :current="request()->routeIs('funding.*')"
            >
                {{ __('nav.fundings') }}
            </flux:navlist.item>

        </flux:sidebar.group>


    </flux:sidebar.nav>

    <flux:sidebar.spacer/>

    {{--    <livewire:app.global.notifications-menu/>--}}
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


            <flux:menu.item icon="bell">
                <flux:modal.trigger name="notifications">
                    {{ __('notification.title') }}
                    <template x-if="notifications.length > 0">
                        <flux:badge size="sm"
                                    variant="solid"
                                    color="red"
                                    class="ml-auto"
                        >
                            <span x-text="notifications.length > 9 ? '9+' : notifications.length"></span>
                        </flux:badge>
                    </template>
                </flux:modal.trigger>
            </flux:menu.item>

            @if(Auth::user()->is_admin)
                <flux:menu.item icon="information-circle"
                                href="/log-viewer"
                                target="_blank"
                >Logs
                </flux:menu.item>
                <flux:menu.item icon="swatch"
                                href="{{ route('settings') }}"
                >Settings
                </flux:menu.item>

            @endif

            <flux:menu.separator/>
            @isMultiLang
            <flux:label>Sprache</flux:label>
            <livewire:app.global.language-switcher/>
            <flux:menu.separator/>
            @endIsMultiLang

            <form method="POST"
                  action="{{ route('logout') }}"
            >
                @csrf
                <flux:menu.item type="submit"
                                icon="arrow-right-start-on-rectangle"
                >{{ __('nav.logout') }}</flux:menu.item>
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
    {{--    <livewire:app.global.notifications-menu/>--}}
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


            <flux:menu.separator/>
            @isMultiLang
            <livewire:app.global.language-switcher/>
            @endIsMultiLang
            <form method="POST"
                  action="{{ route('logout') }}"
            >
                @csrf
                <flux:menu.item type="submit"
                                icon="arrow-right-start-on-rectangle"
                >{{ __('nav.logout') }}</flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>

<flux:main>
    {{ $slot }}
</flux:main>

<flux:modal name="notifications"
            class="w-full max-w-xl"
>

    <div class="flex items-center justify-between mb-4">
        <flux:heading size="lg">{{ __('notification.title') }}</flux:heading>
        <template x-if="notifications.length > 1">
            <flux:button
                    variant="ghost"
                    size="sm"
                    x-on:click="markAsRead('all')"
            >{{ __('notification.mark_all_read') }}</flux:button>
        </template>
    </div>

    <div class="divide-y divide-zinc-100 dark:divide-zinc-800">

        <template x-if="notifications.length === 0">
            <div class="flex flex-col items-center justify-center gap-3 py-12 text-center">
                <flux:icon name="bell-slash"
                           class="size-10 text-zinc-300"
                />
                <flux:text class="text-sm text-zinc-400">{{ __('notification.empty') }}</flux:text>
            </div>
        </template>

        <template x-for="notification in notifications"
                  :key="notification.id"
        >
            <div class="flex items-start gap-3 py-4">

                {{-- Icon --}}
                <div class="shrink-0 mt-0.5">
                    <template x-if="notification.data.type === 'new_applicant'">
                        <flux:icon name="user-plus"
                                   class="size-5 text-blue-500"
                        />
                    </template>
                    <template x-if="notification.data.type === 'application_verified'">
                        <flux:icon name="check-badge"
                                   class="size-5 text-green-500"
                        />
                    </template>
                    <template x-if="notification.data.type === 'application_accepted'">
                        <flux:icon name="check-circle"
                                   class="size-5 text-green-600"
                        />
                    </template>
                    <template x-if="notification.data.type === 'application_rejected'">
                        <flux:icon name="x-circle"
                                   class="size-5 text-red-500"
                        />
                    </template>
                    <template x-if="notification.data.type === 'member_change_request'">
                        <flux:icon name="pencil-square"
                                   class="size-5 text-yellow-500"
                        />
                    </template>
                    <template x-if="notification.data.type === 'member_cancellation_request'">
                        <flux:icon name="arrow-right-start-on-rectangle"
                                   class="size-5 text-orange-500"
                        />
                    </template>
                    <template x-if="!['new_applicant','application_verified','application_accepted','application_rejected','member_change_request','member_cancellation_request'].includes(notification.data.type)">
                        <flux:icon name="bell"
                                   class="size-5 text-zinc-400"
                        />
                    </template>
                </div>

                {{-- Inhalt --}}
                <div class="flex-1 min-w-0 space-y-1">
                    <flux:text class="text-sm leading-snug"
                               x-text="notification.data.message"
                    />
                    <template x-if="notification.data.member_name">
                        <flux:text class="text-xs text-zinc-500 truncate"
                                   x-text="notification.data.member_name"
                        />
                    </template>
                    <template x-if="!notification.data.member_name && notification.data.name">
                        <flux:text class="text-xs text-zinc-500 truncate"
                                   x-text="notification.data.name"
                        />
                    </template>
                    <flux:text
                            class="text-xs text-zinc-400"
                            x-text="notification.created_at"
                    />
                </div>

                {{-- Aktionen --}}
                <div class="flex items-center gap-1 shrink-0">
                    <template x-if="notification.data.url">
                        <a :href="notification.data.url"
                           x-on:click="markAsRead(notification.id)"
                        >
                            <flux:button variant="ghost"
                                         size="xs"
                                         icon="arrow-top-right-on-square"
                            />
                        </a>
                    </template>
                    <flux:button
                            variant="ghost"
                            size="xs"
                            icon="check"
                            x-on:click="markAsRead(notification.id)"
                    />
                </div>
            </div>
        </template>
    </div>
</flux:modal>
@fluxScripts
@persist('toast')
<flux:toast position="top right"/>
@endpersist
@if(config('app.is_demo'))
    <livewire:app.demo-banner/>
@endif
<livewire:app.global.command-palette/>
</body>
</html>

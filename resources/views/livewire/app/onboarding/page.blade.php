<div class="max-w-4xl mx-auto py-10 px-4">
    {{-- Step Navigation --}}
    <nav aria-label="Progress"
         class="mb-10"
    >
        <ol role="list"
            class="divide-y divide-gray-300 dark:divide-white/15 rounded-xl border border-gray-300 dark:border-white/15 md:flex md:divide-y-0 overflow-hidden"
        >

            <li class="relative md:flex md:flex-1">
                @if($step > 1)
                    <x-steps.completed :item="1"
                                       step="01"
                                       label="Organisation"
                    />
                @elseif($step === 1)
                    <x-steps.current :item="1"
                                     step="01"
                                     label="Organisation"
                    />
                @else
                    <x-steps.upcomming :item="1"
                                       step="01"
                                       label="Organisation"
                    />
                @endif
            </li>

            <li class="relative md:flex md:flex-1">
                @if($step > 2)
                    <x-steps.completed :item="2"
                                       step="02"
                                       label="Einstellungen"
                    />
                @elseif($step === 2)
                    <x-steps.current :item="2"
                                     step="02"
                                     label="Einstellungen"
                    />
                @else
                    <x-steps.upcomming :item="2"
                                       step="02"
                                       label="Einstellungen"
                    />
                @endif
            </li>

            <li class="relative md:flex md:flex-1">
                @if($step > 3)
                    <x-steps.completed :item="3"
                                       step="03"
                                       label="Team einladen"
                    />
                @elseif($step === 3)
                    <x-steps.current :item="3"
                                     step="03"
                                     label="Team einladen"
                    />
                @else
                    <x-steps.upcomming :item="3"
                                       step="03"
                                       label="Team einladen"
                    />
                @endif
            </li>

            <li class="relative md:flex md:flex-1">
                @if($step === 4)
                    <x-steps.current :item="4"
                                     step="04"
                                     label="Fertig"
                                     :last="true"
                    />
                @else
                    <x-steps.upcomming :item="4"
                                       step="04"
                                       label="Fertig"
                                       :last="true"
                    />
                @endif
            </li>

        </ol>
    </nav>

    {{-- Step 1: Organisation & Rechtliches --}}
    @if($step === 1)
        <div class="space-y-6">
            <flux:card>
                <flux:heading size="lg">Organisation</flux:heading>
                <flux:subheading>Grundlegende Informationen zu deiner Organisation.</flux:subheading>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <flux:input wire:model="org_name"
                                    label="Organisationsname"
                                    required
                        />
                    </div>
                    <flux:input wire:model="org_email"
                                label="E-Mail"
                                type="email"
                    />
                    <flux:input wire:model="org_web"
                                label="Website"
                                type="url"
                                placeholder="https://"
                    />
                    <flux:input wire:model="org_address"
                                label="Adresse"
                    />
                    <div class="grid grid-cols-3 gap-2">
                        <flux:input wire:model="org_zip"
                                    label="PLZ"
                        />
                        <div class="col-span-2">
                            <flux:input wire:model="org_city"
                                        label="Stadt"
                            />
                        </div>
                    </div>
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg">Rechtliches</flux:heading>
                <flux:subheading>Diese Angaben werden für Belege und Berichte verwendet.</flux:subheading>

                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input wire:model="org_register_id"
                                label="Vereinsregister-Nr."
                                placeholder="VR 12345"
                    />
                    <flux:input wire:model="org_registered_date"
                                label="Eingetragen am"
                                type="date"
                    />
                    <flux:input wire:model="org_court"
                                label="Amtsgericht"
                    />
                    <flux:input wire:model="org_tax_id"
                                label="Steuer-ID"
                    />
                    <flux:input wire:model="org_vat_id"
                                label="USt-ID"
                                placeholder="DE123456789"
                    />
                </div>
            </flux:card>

            <div class="flex justify-end">
                <flux:button variant="primary"
                             wire:click="nextStep"
                >Weiter
                </flux:button>
            </div>
        </div>
    @endif

    {{-- Step 2: Einstellungen --}}
    @if($step === 2)
        <div class="space-y-6">
            <flux:card>
                <flux:heading size="lg">Geschäftsjahr</flux:heading>
                <flux:subheading>Das Startjahr für die Buchhaltung.</flux:subheading>
                <div class="mt-6">
                    <flux:input wire:model="fiscal_year"
                                label="Startjahr"
                                type="number"
                                min="2000"
                                max="2100"
                    />
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg">Sprachen</flux:heading>
                <flux:subheading>Welche Sprachen sollen in deiner Instanz aktiv sein?</flux:subheading>
                <flux:separator class="my-4" text="verfügbare Sprachen"/>
                <flux:checkbox.group wire:model.live="active_locales">
                    @foreach(\App\Models\Locale::all() as $locale)
                        <flux:checkbox
                                       value="{{ $locale->name }}"
                                       label="{{ $locale->label() }}"
                                       description="{{ $locale->description() }}"
                        />
                    @endforeach
                </flux:checkbox.group>
                <flux:error name="active_locales" class="mt-2" />
            </flux:card>

            <div class="flex justify-between">
                <flux:button wire:click="prevStep">Zurück</flux:button>
                <flux:button variant="primary"
                             wire:click="nextStep"
                >Weiter
                </flux:button>
            </div>
        </div>
    @endif

    {{-- Step 3: Profil & Team einladen --}}
    @if($step === 3)
        <div class="space-y-6">

            <flux:card>
                <flux:heading size="lg">Dein Profil</flux:heading>
                <flux:subheading>Vervollständige deine eigenen Angaben.</flux:subheading>
                <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <flux:input wire:model="user_name"
                                label="Nachname"
                                required
                    />
                    <flux:input wire:model="user_first_name"
                                label="Vorname"
                    />
                    <flux:input wire:model="user_username"
                                label="Benutzername"
                    />
                </div>
            </flux:card>

            <flux:card>
                <flux:heading size="lg">Team einladen</flux:heading>
                <flux:subheading>
                    Lade weitere Personen ein. Jede eingeladene Person wird automatisch als Mitglied angelegt –
                    nicht jedes Mitglied hat automatisch einen Login.
                </flux:subheading>

                <div class="mt-6 space-y-4">
                    @foreach($invites as $index => $invite)
                        <div class="flex gap-2 items-start">
                            <div class="grid grid-cols-3 gap-2 flex-1">
                                <flux:input
                                        wire:model="invites.{{ $index }}.name"
                                        placeholder="Nachname"
                                        required
                                />
                                <flux:input
                                        wire:model="invites.{{ $index }}.first_name"
                                        placeholder="Vorname"
                                />
                                <flux:input
                                        wire:model="invites.{{ $index }}.email"
                                        type="email"
                                        placeholder="email@beispiel.de"
                                />
                            </div>
                            @if(count($invites) > 1)
                                <flux:button variant="ghost"
                                             wire:click="removeInvite({{ $index }})"
                                             icon="trash"
                                />
                            @endif
                        </div>
                    @endforeach

                    <flux:button variant="ghost"
                                 wire:click="addInvite"
                                 icon="plus"
                    >
                        Weitere hinzufügen
                    </flux:button>
                </div>
            </flux:card>

            @if(! $smtpConfigured)
                <flux:callout variant="warning" class="my-6">
                    <flux:callout.heading icon="envelope">Hinweis</flux:callout.heading>

                    <flux:callout.text>Aktuell werden in dieser Instanz alle ausgehenden E-Mail in den log geschrieben und nicht verschickt. Bitte wenden Sie sich an unseren Helpdesk, wenn Sie die Mögkeit des E-Mail versandes nutzen möchten. Danke!</flux:callout.text>
                </flux:callout>
            @endif

            <div class="flex justify-between">
                <flux:button wire:click="prevStep">Zurück</flux:button>
                <flux:button variant="primary"
                             wire:click="nextStep"
                >Weiter
                </flux:button>
            </div>
        </div>
    @endif

    {{-- Step 4: Fertig --}}
    @if($step === 4)
        <div class="space-y-6">
            <flux:card>
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-teal-100 dark:bg-teal-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <flux:icon.check class="w-8 h-8 text-teal-600 dark:text-teal-400"/>
                    </div>
                    <flux:heading size="xl">Alles bereit!</flux:heading>
                    <flux:subheading class="mt-2">
                        Deine Organisation ist eingerichtet. Du kannst jetzt loslegen.
                    </flux:subheading>

                    <div class="mt-8 text-left max-w-sm mx-auto space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <div class="flex justify-between items-center">
                            <strong>{{ $org_name }}</strong>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 stroke-teal-800">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            </div>
                        <div class="flex justify-between items-center">
                            <span> Geschäftsjahr {{ $fiscal_year }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 stroke-teal-800">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                       </div>
                        <div class="flex justify-between items-center">
                            @if (count($active_locales) > 1)

                                <div>Ausgewählte Sprachen</div>
                                <aside>
                                @for($i = 0; $i < count($active_locales); $i++)
                                        <flux:badge color="teal" size="sm">{{ $active_locales[$i] }}</flux:badge>
                                @endfor
                                </aside>
                            @else

                                <span>Ausgewählte Sprache</span>
                                <flux:badge color="teal" size="sm">{{ $active_locales[0] }}</flux:badge>

                            @endif
                        </div>

                        @php $validInvites = array_filter($invites, fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL)) @endphp
                        @if(count($validInvites) > 0)
                            <p class="flex justify-between items-center">


                                {{ count($validInvites) }} Einladung(en) werden versendet</p>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                        @endif
                    </div>
                </div>
            </flux:card>

            <div class="flex justify-between">
                <flux:button wire:click="prevStep">Zurück</flux:button>
                <flux:button variant="primary"
                             wire:click="finish"
                >
                    Zum Dashboard
                </flux:button>
            </div>
        </div>
    @endif

</div>
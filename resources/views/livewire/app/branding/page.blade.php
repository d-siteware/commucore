<div class="space-y-6">

    <flux:heading size="lg">Branding</flux:heading>
    <flux:subheading>Farben & Erscheinungsbild</flux:subheading>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-9">
        <flux:card class="space-y-4">
            <flux:heading size="sm">Light Mode</flux:heading>
            <div class="grid grid-cols-6 gap-6">
                <flux:input type="color"
                            label="primary"
                            wire:model.live="form.primary"
                />
                <flux:input type="color"
                            label="secondary"
                            wire:model.live="form.secondary"
                />
                <flux:input type="color"
                            label="brand"
                            wire:model.live="form.brand"
                />
                <flux:input type="color"
                            label="bg"
                            wire:model.live="form.bg"
                />
                <flux:input type="color"
                            label="text"
                            wire:model.live="form.text"
                />
                <flux:input type="color"
                            label="positive"
                            wire:model.live="form.positive"
                />
                <flux:input type="color"
                            label="negative"
                            wire:model.live="form.negative"
                />
                <flux:input type="color"
                            label="storno"
                            wire:model.live="form.storno"
                />
                <flux:input type="color"
                            label="accent"
                            wire:model.live="form.accent"
                />
                <flux:input type="color"
                            label="accent_foreground"
                            wire:model.live="form.accent_foreground"
                />
                <flux:input type="color"
                            label="accent_content"
                            wire:model.live="form.accent_content"
                />
            </div>


        </flux:card>

        <flux:card class="space-y-4">
            <flux:heading size="sm">Dark Mode</flux:heading>
            <div class="grid grid-cols-6 gap-6">
                <flux:input type="color"
                            label="primary"
                            wire:model.live="form.primary_dark"
                />
                <flux:input type="color"
                            label="secondary"
                            wire:model.live="form.secondary_dark"
                />
                <flux:input type="color"
                            label="brand"
                            wire:model.live="form.brand_dark"
                />
                <flux:input type="color"
                            label="bg"
                            wire:model.live="form.bg_dark"
                />
                <flux:input type="color"
                            label="text"
                            wire:model.live="form.text_dark"
                />
                <flux:input type="color"
                            label="positive"
                            wire:model.live="form.positive_dark"
                />
                <flux:input type="color"
                            label="negative"
                            wire:model.live="form.negative_dark"
                />
                <flux:input type="color"
                            label="storno"
                            wire:model.live="form.storno_dark"
                />
                <flux:input type="color"
                            label="accent"
                            wire:model.live="form.accent_dark"
                />
                <flux:input type="color"
                            label="accent_foreground"
                            wire:model.live="form.accent_foreground_dark"
                />
                <flux:input type="color"
                            label="accent_content"
                            wire:model.live="form.accent_content_dark"
                />
            </div>

        </flux:card>
    </div>

    {{-- Organization Section --}}
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Organisation</flux:heading>
            <flux:subheading>Grundlegende Informationen über Ihre Organisation</flux:subheading>
        </div>

        <flux:separator />

        <div class="grid gap-6 md:grid-cols-2">
            <flux:input
                    wire:model="form.organization_name"
                    label="Name der Organisation"
                    required
            />

            <flux:input
                    wire:model="form.organization_email"
                    label="E-Mail"
                    type="email"
                    required
            />

            <flux:input
                    wire:model="form.organization_web"
                    label="Website"
                    type="url"
                    required
                    class="md:col-span-2"
            />

            <flux:input
                    wire:model="form.organization_slogan"
                    label="Motto / Slogan"
                    placeholder="z.B. Gemeinsam mehr erreichen"
                    class="md:col-span-2"
            />

            <flux:textarea
                    wire:model="form.organization_description"
                    label="Kurzbeschreibung"
                    rows="3"
                    placeholder="Eine kurze Beschreibung Ihrer Organisation"
                    class="md:col-span-2"
            />
        </div>
    </flux:card>

    {{-- Logo Section --}}
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Logo</flux:heading>
            <flux:subheading>Passen Sie das Erscheinungsbild Ihrer Anwendung an</flux:subheading>
        </div>

        <flux:separator />

        <div class="flex items-start gap-6">
            <div class="shrink-0">
                <div class="w-32 h-32 border rounded-lg flex items-center justify-center bg-gray-50 dark:bg-gray-800 p-4">
                    @php
                        $logo = app(\App\Services\SettingsService::class)->getLogo();
                    @endphp

                    @if($logo)
                        <img src="{{ $logo }}" alt="Logo" class="max-w-full max-h-full object-contain">
                    @else
                        <x-application-logo class="w-full h-full" />
                    @endif
                </div>
            </div>

            <div class="flex-1 space-y-4">
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Aktuelles Logo
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if(app(\App\Services\SettingsService::class)->getLogo())
                            Individuelles Logo wird verwendet
                        @else
                            Standard-Logo-Komponente wird verwendet
                        @endif
                    </p>
                </div>

                @if($showLogoUpload)
                    <div class="space-y-3">
                        <flux:input
                                type="file"
                                wire:model="newLogo"
                                accept="image/*"
                                label="Neue Logo-Datei auswählen"
                        />

                        @if ($newLogo)
                            <div class="flex gap-2">
                                <flux:button
                                        wire:click="uploadLogo"
                                        variant="primary"
                                >
                                    Hochladen
                                </flux:button>
                                <flux:button
                                        wire:click="$set('newLogo', null); $set('showLogoUpload', false)"
                                        variant="ghost"
                                >
                                    Abbrechen
                                </flux:button>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex gap-2">
                        <flux:button
                                wire:click="$set('showLogoUpload', true)"
                                variant="primary"
                        >
                            Neues Logo hochladen
                        </flux:button>

                        @if(app(\App\Services\SettingsService::class)->getLogo())
                            <flux:button
                                    wire:click="resetLogo"
                                    wire:confirm="Möchten Sie wirklich zum Standard-Logo zurückkehren?"
                                    variant="ghost"
                            >
                                Zurücksetzen
                            </flux:button>
                        @endif
                    </div>
                @endif

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Empfohlen: PNG oder SVG mit transparentem Hintergrund, max. 2 MB
                </p>
            </div>
        </div>
    </flux:card>

    {{-- Favicon Section --}}
    <flux:card class="space-y-6">
        <div>
            <flux:heading size="lg">Favicon</flux:heading>
            <flux:subheading>Das kleine Icon im Browser-Tab</flux:subheading>
        </div>

        <flux:separator />

        <div class="flex items-start gap-6">
            <div class="shrink-0">
                <div class="w-16 h-16 border rounded-lg flex items-center justify-center bg-gray-50 dark:bg-gray-800 p-2">
                    <img
                            src="{{ app(\App\Services\SettingsService::class)->getFavicon() }}"
                            alt="Favicon"
                            class="w-full h-full object-contain"
                    >
                </div>
            </div>

            <div class="flex-1 space-y-4">
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Aktuelles Favicon
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @if(app(\App\Services\SettingsService::class)->get('branding.favicon'))
                            Individuelles Favicon wird verwendet
                        @else
                            Standard-Favicon wird verwendet
                        @endif
                    </p>
                </div>

                @if($showFaviconUpload)
                    <div class="space-y-3">
                        <flux:input
                                type="file"
                                wire:model="newFavicon"
                                accept=".png,.ico"
                                label="Neue Favicon-Datei auswählen"
                        />

                        @if ($newFavicon)
                            <div class="flex gap-2">
                                <flux:button
                                        wire:click="uploadFavicon"
                                        variant="primary"
                                >
                                    Hochladen
                                </flux:button>
                                <flux:button
                                        wire:click="$set('newFavicon', null); $set('showFaviconUpload', false)"
                                        variant="ghost"
                                >
                                    Abbrechen
                                </flux:button>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="flex gap-2">
                        <flux:button
                                wire:click="$set('showFaviconUpload', true)"
                                variant="primary"
                        >
                            Neues Favicon hochladen
                        </flux:button>

                        @if(app(\App\Services\SettingsService::class)->get('branding.favicon'))
                            <flux:button
                                    wire:click="resetFavicon"
                                    wire:confirm="Möchten Sie wirklich zum Standard-Favicon zurückkehren?"
                                    variant="ghost"
                            >
                                Zurücksetzen
                            </flux:button>
                        @endif
                    </div>
                @endif

                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Empfohlen: 32x32 oder 64x64 Pixel, PNG oder ICO, max. 512 KB
                </p>
            </div>
        </div>
    </flux:card>

    <flux:button variant="primary"
                 wire:click="save"
    >
        Speichern
    </flux:button>

    <flux:button
            variant="ghost"
            wire:click="restoreDefaults"
    >
        Auf Standard zurücksetzen
    </flux:button>

</div>

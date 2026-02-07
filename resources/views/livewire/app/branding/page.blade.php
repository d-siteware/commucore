<div class="space-y-6">

    <flux:heading size="lg">Branding</flux:heading>
    <flux:subheading>Farben & Erscheinungsbild Ihrer Organisation</flux:subheading>
    <flux:tab.group>
        <flux:tabs wire:model="currentTab">
            <flux:tab name="colors">Farben</flux:tab>
            <flux:tab name="organization">Organisation</flux:tab>
        </flux:tabs>
        <flux:tab.panel name="colors"
                        label="Farben & Design"
        >
            <div class="space-y-6 pt-6">

                {{-- Color Editor Section --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- Light Mode --}}
                    <flux:card class="space-y-6">
                        <div>
                            <flux:heading size="lg">Light Mode</flux:heading>
                            <flux:subheading>Farbschema für helle Darstellung</flux:subheading>
                        </div>

                        <flux:separator/>

                        {{-- Color Selector --}}
                        <div class="space-y-4">
                            <flux:select variant="listbox"
                                         searchable
                                         wire:model.live="selectedLightColor"
                                         placeholder="Farbe zum Bearbeiten wählen..."
                            >
                                <flux:select.option value="primary">Primary - Hauptfarbe</flux:select.option>
                                <flux:select.option value="secondary">Secondary - Sekundärfarbe</flux:select.option>
                                <flux:select.option value="brand">Brand - Markenfarbe</flux:select.option>
                                <flux:select.option value="bg">Background - Hintergrund</flux:select.option>
                                <flux:select.option value="text">Text - Textfarbe</flux:select.option>
                                <flux:select.option value="positive">Positive - Erfolg/Positiv</flux:select.option>
                                <flux:select.option value="negative">Negative - Fehler/Negativ</flux:select.option>
                                <flux:select.option value="storno">Storno - Stornierung</flux:select.option>
                                <flux:select.option value="accent">Accent - Akzent</flux:select.option>
                                <flux:select.option value="acent_foreground">Accent Foreground - Akzent Vordergrund</flux:select.option>
                                <flux:select.option value="accent_content">Accent Content - Akzent Inhalt</flux:select.option>
                            </flux:select>


                            @if($selectedLightColor)
                                <div class="flex items-center gap-4">
                                    @if($selectedLightColor === 'primary')
                                        <flux:input type="color"
                                                    wire:model.live="form.primary"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.primary"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedLightColor === 'secondary')
                                        <flux:input type="color"
                                                    wire:model.live="form.secondary"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.secondary"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedLightColor === 'brand')
                                        <flux:input type="color"
                                                    wire:model.live="form.brand"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.brand"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedLightColor === 'bg')
                                        <flux:input type="color"
                                                    wire:model.live="form.bg"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.bg"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedLightColor === 'text')
                                        <flux:input type="color"
                                                    wire:model.live="form.text"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.text"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedLightColor === 'positive')
                                        <flux:input type="color"
                                                    wire:model.live="form.positive"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.positive"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedLightColor === 'negative')
                                        <flux:input type="color"
                                                    wire:model.live="form.negative"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.negative"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedLightColor === 'storno')
                                        <flux:input type="color"
                                                    wire:model.live="form.storno"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.storno"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedLightColor === 'accent')
                                        <flux:input type="color"
                                                    wire:model.live="form.accent"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.accent"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedLightColor === 'accent_foreground')
                                        <flux:input type="color"
                                                    wire:model.live="form.accent_foreground"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.accent_foreground"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedLightColor === 'accent_content')
                                        <flux:input type="color"
                                                    wire:model.live="form.accent_content"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.accent_content"
                                                    placeholder="#000000"
                                        />
                                    @endif
                                </div>
                            @endif
                        </div>

                        <flux:separator/>

                        {{-- Mock Preview Light --}}
                        <div class="space-y-3">
                            <flux:subheading>Vorschau</flux:subheading>
                            <div class="border rounded-lg p-6 space-y-4"
                                 style="background-color: {{ $form->bg }}; color: {{ $form->text }};"
                            >
                                <div class="flex items-center justify-between pb-3 border-b"
                                     style="border-color: {{ $form->primary }}20;"
                                >
                                    <h3 class="font-semibold"
                                        style="color: {{ $form->primary }};"
                                    >
                                        Beispiel Überschrift
                                    </h3>
                                    <span class="text-sm"
                                          style="color: {{ $form->secondary }};"
                                    >
                                        Sekundär Text
                                    </span>
                                </div>

                                <p class="text-sm"
                                   style="color: {{ $form->text }};"
                                >
                                    Dies ist ein Beispieltext in der normalen Textfarbe.
                                </p>

                                <div class="flex gap-2 flex-wrap">
                                    <button class="px-3 py-1.5 rounded text-sm font-medium"
                                            style="background-color: {{ $form->accent }}; color: {{ $form->accent_foreground }};"
                                    >
                                        Akzent Button
                                    </button>
                                    <button class="px-3 py-1.5 rounded text-sm font-medium"
                                            style="background-color: {{ $form->primary }}; color: {{ $form->bg }};"
                                    >
                                        Primary Button
                                    </button>
                                    <button class="px-3 py-1.5 rounded text-sm"
                                            style="border: 1px solid {{ $form->secondary }}; color: {{ $form->secondary }};"
                                    >
                                        Secondary
                                    </button>
                                </div>

                                <div class="flex gap-2 flex-wrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->positive }}20; color: {{ $form->positive }};"
                                    >
                                        ✓ Erfolg
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->negative }}20; color: {{ $form->negative }};"
                                    >
                                        ✗ Fehler
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->storno }}20; color: {{ $form->storno }};"
                                    >
                                        ○ Storno
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->brand }}20; color: {{ $form->brand }};"
                                    >
                                        ★ Brand
                                    </span>
                                </div>

                                <div class="border rounded p-4 space-y-2"
                                     style="border-color: {{ $form->primary }}20; background-color: {{ $form->accent }}10;"
                                >
                                    <h4 class="font-medium"
                                        style="color: {{ $form->accent_content }};"
                                    >
                                        Beispiel Card
                                    </h4>
                                    <p class="text-sm"
                                       style="color: {{ $form->text }};"
                                    >
                                        Mit Akzent-Hintergrund und Inhalt.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </flux:card>

                    {{-- Dark Mode --}}
                    <flux:card class="space-y-6">
                        <div>
                            <flux:heading size="lg">Dark Mode</flux:heading>
                            <flux:subheading>Farbschema für dunkle Darstellung</flux:subheading>
                        </div>

                        <flux:separator/>

                        {{-- Color Selector --}}
                        <div class="space-y-4">
                           <flux:select
                                    variant="listbox"
                                    wire:model.live="selectedDarkColor"
                                    placeholder="Farbe zum Bearbeiten wählen..."
                            >
                                <flux:select.option value="primary_dark">Primary - Hauptfarbe</flux:select.option>
                                <flux:select.option value="secondary_dark">Secondary - Sekundärfarbe</flux:select.option>
                                <flux:select.option value="brand_dark">Brand - Markenfarbe</flux:select.option>
                                <flux:select.option value="bg_dark">Background - Hintergrund</flux:select.option>
                                <flux:select.option value="text_dark">Text - Textfarbe</flux:select.option>
                                <flux:select.option value="positive_dark">Positive - Erfolg/Positiv</flux:select.option>
                                <flux:select.option value="negative_dark">Negative - Fehler/Negativ</flux:select.option>
                                <flux:select.option value="storno_dark">Storno - Stornierung</flux:select.option>
                                <flux:select.option value="accent_dark">Accent - Akzent</flux:select.option>
                                <flux:select.option value="accent_foreground_dark">Accent Forend - Akzent Vordergrund</flux:select.option>
                                <flux:select.option value="accent_content_dark">Accent Content - Akzent Inhalt</flux:select.option>
                            </flux:select>

                        @if($selectedDarkColor)
                            <div class="flex items-center gap-4">
                                @if($selectedDarkColor === 'primary_dark')
                                    <flux:input type="color" wire:model.live="form.primary_dark" class="w-24" />
                                    <flux:input wire:model.live="form.primary_dark" placeholder="#000000"/>
                                @elseif($selectedDarkColor === 'secondary_dark')
                                    <flux:input type="color" wire:model.live="form.secondary_dark" class="w-24" />
                                    <flux:input wire:model.live="form.secondary_dark" placeholder="#000000"/>
                                @elseif($selectedDarkColor === 'brand_dark')
                                    <flux:input type="color" wire:model.live="form.brand_dark" class="w-24" />
                                    <flux:input wire:model.live="form.brand_dark" placeholder="#000000"/>
                                @elseif($selectedDarkColor === 'bg_dark')
                                    <flux:input type="color" wire:model.live="form.bg_dark" class="w-24" />
                                    <flux:input wire:model.live="form.bg_dark" placeholder="#000000"/>
                                @elseif($selectedDarkColor === 'text_dark')
                                    <flux:input type="color" wire:model.live="form.text_dark" class="w-24" />
                                    <flux:input wire:model.live="form.text_dark" placeholder="#000000"/>
                                @elseif($selectedDarkColor === 'positive_dark')
                                    <flux:input type="color" wire:model.live="form.positive_dark" class="w-24" />
                                    <flux:input wire:model.live="form.positive_dark" placeholder="#000000"/>
                                @elseif($selectedDarkColor === 'negative_dark')
                                    <flux:input type="color" wire:model.live="form.negative_dark" class="w-24" />
                                    <flux:input wire:model.live="form.negative_dark" placeholder="#000000"/>
                                @elseif($selectedDarkColor === 'storno_dark')
                                    <flux:input type="color" wire:model.live="form.storno_dark" class="w-24" />
                                    <flux:input wire:model.live="form.storno_dark" placeholder="#000000"/>
                                @elseif($selectedDarkColor === 'accent_dark')
                                    <flux:input type="color" wire:model.live="form.accent_dark" class="w-24" />
                                    <flux:input wire:model.live="form.accent_dark" placeholder="#000000"/>
                                @elseif($selectedDarkColor === 'accent_foreground_dark')
                                    <flux:input type="color" wire:model.live="form.accent_foreground_dark" class="w-24" />
                                    <flux:input wire:model.live="form.accent_foreground_dark" placeholder="#000000"/>
                                @elseif($selectedDarkColor === 'accent_content_dark')
                                    <flux:input type="color" wire:model.live="form.accent_content_dark" class="w-24" />
                                    <flux:input wire:model.live="form.accent_content_dark" placeholder="#000000"/>
                                @endif
                            </div>
                        @endif
                        </div>

                        <flux:separator/>

                        {{-- Mock Preview Dark --}}
                        <div class="space-y-3">
                            <flux:subheading>Vorschau</flux:subheading>
                            <div class="border rounded-lg p-6 space-y-4"
                                 style="background-color: {{ $form->bg_dark }}; color: {{ $form->text_dark }};"
                            >

                                <div class="flex items-center justify-between pb-3 border-b"
                                     style="border-color: {{ $form->primary_dark }}40;"
                                >
                                    <h3 class="font-semibold"
                                        style="color: {{ $form->primary_dark }};"
                                    >
                                        Beispiel Überschrift
                                    </h3>
                                    <span class="text-sm"
                                          style="color: {{ $form->secondary_dark }};"
                                    >
                                        Sekundär Text
                                    </span>
                                </div>

                                <p class="text-sm"
                                   style="color: {{ $form->text_dark }};"
                                >
                                    Dies ist ein Beispieltext in der normalen Textfarbe.
                                </p>

                                <div class="flex gap-2 flex-wrap">
                                    <button class="px-3 py-1.5 rounded text-sm font-medium"
                                            style="background-color: {{ $form->accent_dark }}; color: {{ $form->accent_foreground_dark }};"
                                    >
                                        Akzent Button
                                    </button>
                                    <button class="px-3 py-1.5 rounded text-sm font-medium"
                                            style="background-color: {{ $form->primary_dark }}; color: {{ $form->bg_dark }};"
                                    >
                                        Primary Button
                                    </button>
                                    <button class="px-3 py-1.5 rounded text-sm"
                                            style="border: 1px solid {{ $form->secondary_dark }}; color: {{ $form->secondary_dark }};"
                                    >
                                        Secondary
                                    </button>
                                </div>

                                <div class="flex gap-2 flex-wrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->positive_dark }}30; color: {{ $form->positive_dark }};"
                                    >
                                        ✓ Erfolg
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->negative_dark }}30; color: {{ $form->negative_dark }};"
                                    >
                                        ✗ Fehler
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->storno_dark }}30; color: {{ $form->storno_dark }};"
                                    >
                                        ○ Storno
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->brand_dark }}30; color: {{ $form->brand_dark }};"
                                    >
                                        ★ Brand
                                    </span>
                                </div>

                                <div class="border rounded p-4 space-y-2"
                                     style="border-color: {{ $form->primary_dark }}40; background-color: {{ $form->accent_dark }}20;"
                                >
                                    <h4 class="font-medium"
                                        style="color: {{ $form->accent_content_dark }};"
                                    >
                                        Beispiel Card
                                    </h4>
                                    <p class="text-sm"
                                       style="color: {{ $form->text_dark }};"
                                    >
                                        Mit Akzent-Hintergrund und Inhalt.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </flux:card>
                </div>
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="organization"
                        label="Organisation"
        >
            <div class="space-y-6 pt-6">

                {{-- Organization Section --}}
                <flux:card class="space-y-6">
                    <div>
                        <flux:heading size="lg">Organisation</flux:heading>
                        <flux:subheading>Grundlegende Informationen über Ihre Organisation</flux:subheading>
                    </div>

                    <flux:separator/>

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

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    {{-- Logo Section --}}
                    <flux:card class="space-y-6">
                        <div>
                            <flux:heading size="lg">Logo</flux:heading>
                            <flux:subheading>Passen Sie das Erscheinungsbild Ihrer Anwendung an</flux:subheading>
                        </div>

                        <flux:separator/>

                        <flux:callout
                                variant="warning"
                                icon="exclamation-triangle"
                                heading="SVG-Dateien werden automatisch bereinigt"
                        >
                            SVG-Dateien können potenziell schädlichen Code enthalten. Alle hochgeladenen SVG-Dateien werden automatisch bereinigt, um JavaScript und externe Ressourcen zu entfernen.
                        </flux:callout>

                        <div class="flex items-start gap-6">
                            <div class="shrink-0">
                                <div class="w-32 h-32 border rounded-lg flex items-center justify-center bg-gray-50 dark:bg-gray-800 p-4">
                                    @php
                                        $logo = app(\App\Services\SettingsService::class)->getLogo();
                                    @endphp

                                    @if($logo)
                                        <img src="{{ $logo }}"
                                             alt="Logo"
                                             class="max-w-full max-h-full object-contain"
                                        >
                                    @else
                                        <x-application-logo class="w-full h-full"/>
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
                                                accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                                                label="Neue Logo-Datei auswählen"
                                        />

                                        @error('newLogo')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror

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

                        <flux:separator/>

                        <flux:callout
                                variant="warning"
                                icon="exclamation-triangle"
                                heading="SVG-Dateien werden automatisch bereinigt"
                        >
                            SVG-Dateien können potenziell schädlichen Code enthalten. Alle hochgeladenen SVG-Dateien werden automatisch bereinigt, um JavaScript und externe Ressourcen zu entfernen.
                        </flux:callout>

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
                                                accept="image/png,image/x-icon,image/svg+xml,.ico"
                                                label="Neue Favicon-Datei auswählen"
                                        />

                                        @error('newFavicon')
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror

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
                                    Empfohlen: 32x32 oder 64x64 Pixel, PNG, ICO oder SVG, max. 512 KB
                                </p>
                            </div>
                        </div>
                    </flux:card>
                </div>

            </div>
        </flux:tab.panel>
    </flux:tab.group>
    {{-- Action Buttons --}}
    <div class="flex gap-3">
        <flux:button variant="primary"
                     wire:click="save"
        >
            Speichern
        </flux:button>

        <flux:button variant="ghost"
                     wire:click="restoreDefaults"
        >
            Auf Standard zurücksetzen
        </flux:button>
    </div>

</div>
<div class="space-y-6">

    <flux:heading size="lg">{{ __('branding.page.heading') }}</flux:heading>
    <flux:subheading>{{ __('branding.page.subheading') }}</flux:subheading>
    <flux:tab.group>
        <flux:tabs wire:model="selectedTab">
            <flux:tab name="org-info"
                      icon="building-office"
                      wire:click="setSelectedTab('org-info')"
            >
                <span class="hidden lg:inline">{{ __('branding.tab.organization') }}</span>
            </flux:tab>
            <flux:tab name="member-fees"
                      icon="banknotes"
                      wire:click="setSelectedTab('member-fees')"
            >
                <span class="hidden lg:inline">{{ __('branding.tab.fees') }}</span>
            </flux:tab>
            <flux:tab name="org-texts"
                      icon="document-text"
                      wire:click="setSelectedTab('org-texts')"
            >
                <span class="hidden lg:inline">{{ __('branding.tab.texts') }}</span></flux:tab>
            <flux:tab name="org-statute"
                      icon="scale"
                      wire:click="setSelectedTab('org-statute')"
            >
                <span class="hidden lg:inline">{{ __('branding.tab.statute') }}</span></flux:tab>
            <flux:tab name="org-logo"
                      icon="photo"
                      wire:click="setSelectedTab('org-logo')"
            >
                <span class="hidden lg:inline">{{ __('branding.tab.logos') }}</span></flux:tab>
            <flux:tab name="org-colors"
                      icon="swatch"
                      wire:click="setSelectedTab('org-colors')"
            >
                <span class="hidden lg:inline">{{ __('branding.tab.colors') }}</span></flux:tab>
            <flux:tab name="locales"
                      icon="language"
                      wire:click="setSelectedTab('locales')"
            >
                <span class="hidden lg:inline">{{ __('branding.tab.locales') }}</span></flux:tab>
            <flux:tab name="sepa"
                      icon="banknotes"
            >
                <span class="hidden lg:inline">{{ __('sepa.settings.tab') }}</span></flux:tab>
            <flux:tab name="datev"
                      icon="calculator"
            >
                <span class="hidden lg:inline">{{ __('accounting.datev.settings.tab') }}</span></flux:tab>
        </flux:tabs>
        <flux:tab.panel name="org-colors"
                        label="{{ __('branding.tab_panel.colors') }}"
        >
            <div class="space-y-6">

                {{-- Color Editor Section --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- Light Mode --}}
                    <flux:card class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ __('branding.colors.light_mode') }}</flux:heading>
                            <flux:subheading>{{ __('branding.colors.light_mode_desc') }}</flux:subheading>
                        </div>

                        <flux:separator/>

                        {{-- Color Selector --}}
                        <div class="space-y-4">
                            <flux:select variant="listbox"
                                         searchable
                                         wire:model.live="selectedLightColor"
                                         placeholder="{{ __('branding.colors.select_placeholder') }}"
                            >
                                <flux:select.option value="primary">{{ __('branding.colors.primary') }}</flux:select.option>
                                <flux:select.option value="secondary">{{ __('branding.colors.secondary') }}</flux:select.option>
                                <flux:select.option value="brand">{{ __('branding.colors.brand') }}</flux:select.option>
                                <flux:select.option value="bg">{{ __('branding.colors.bg') }}</flux:select.option>
                                <flux:select.option value="text">{{ __('branding.colors.text') }}</flux:select.option>
                                <flux:select.option value="positive">{{ __('branding.colors.positive') }}</flux:select.option>
                                <flux:select.option value="negative">{{ __('branding.colors.negative') }}</flux:select.option>
                                <flux:select.option value="storno">{{ __('branding.colors.storno') }}</flux:select.option>
                                <flux:select.option value="accent">{{ __('branding.colors.accent') }}</flux:select.option>
                                <flux:select.option value="acent_foreground">{{ __('branding.colors.accent_foreground') }}</flux:select.option>
                                <flux:select.option value="accent_content">{{ __('branding.colors.accent_content') }}</flux:select.option>
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
                            <flux:subheading>{{ __('branding.colors.preview') }}</flux:subheading>
                            <div class="border rounded-lg p-6 space-y-4"
                                 style="background-color: {{ $form->bg }}; color: {{ $form->text }};"
                            >
                                <div class="flex items-center justify-between pb-3 border-b"
                                     style="border-color: {{ $form->primary }}20;"
                                >
                                    <h3 class="font-semibold"
                                        style="color: {{ $form->primary }};"
                                    >
                                        {{ __('branding.colors.preview_heading') }}
                                    </h3>
                                    <span class="text-sm"
                                          style="color: {{ $form->secondary }};"
                                    >
                                        {{ __('branding.colors.preview_secondary') }}
                                    </span>
                                </div>

                                <p class="text-sm"
                                   style="color: {{ $form->text }};"
                                >
                                    {{ __('branding.colors.preview_body') }}
                                </p>

                                <div class="flex gap-2 flex-wrap">
                                    <button class="px-3 py-1.5 rounded text-sm font-medium"
                                            style="background-color: {{ $form->accent }}; color: {{ $form->accent_foreground }};"
                                    >
                                        {{ __('branding.colors.preview_accent_btn') }}
                                    </button>
                                    <button class="px-3 py-1.5 rounded text-sm font-medium"
                                            style="background-color: {{ $form->primary }}; color: {{ $form->bg }};"
                                    >
                                        {{ __('branding.colors.preview_primary_btn') }}
                                    </button>
                                    <button class="px-3 py-1.5 rounded text-sm"
                                            style="border: 1px solid {{ $form->secondary }}; color: {{ $form->secondary }};"
                                    >
                                        {{ __('branding.colors.preview_secondary_btn') }}
                                    </button>
                                </div>

                                <div class="flex gap-2 flex-wrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->positive }}20; color: {{ $form->positive }};"
                                    >
                                        {{ __('branding.colors.preview_success') }}
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->negative }}20; color: {{ $form->negative }};"
                                    >
                                        {{ __('branding.colors.preview_error') }}
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->storno }}20; color: {{ $form->storno }};"
                                    >
                                        {{ __('branding.colors.preview_storno') }}
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->brand }}20; color: {{ $form->brand }};"
                                    >
                                        {{ __('branding.colors.preview_brand') }}
                                    </span>
                                </div>

                                <div class="border rounded p-4 space-y-2"
                                     style="border-color: {{ $form->primary }}20; background-color: {{ $form->accent }}10;"
                                >
                                    <h4 class="font-medium"
                                        style="color: {{ $form->accent_content }};"
                                    >
                                        {{ __('branding.colors.preview_card_heading') }}
                                    </h4>
                                    <p class="text-sm"
                                       style="color: {{ $form->text }};"
                                    >
                                        {{ __('branding.colors.preview_card_body') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </flux:card>

                    {{-- Dark Mode --}}
                    <flux:card class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ __('branding.colors.dark_mode') }}</flux:heading>
                            <flux:subheading>{{ __('branding.colors.dark_mode_desc') }}</flux:subheading>
                        </div>

                        <flux:separator/>

                        {{-- Color Selector --}}
                        <div class="space-y-4">
                            <flux:select
                                    variant="listbox"
                                    wire:model.live="selectedDarkColor"
                                    placeholder="{{ __('branding.colors.select_placeholder') }}"
                            >
                                <flux:select.option value="primary_dark">{{ __('branding.colors.primary') }}</flux:select.option>
                                <flux:select.option value="secondary_dark">{{ __('branding.colors.secondary') }}</flux:select.option>
                                <flux:select.option value="brand_dark">{{ __('branding.colors.brand') }}</flux:select.option>
                                <flux:select.option value="bg_dark">{{ __('branding.colors.bg') }}</flux:select.option>
                                <flux:select.option value="text_dark">{{ __('branding.colors.text') }}</flux:select.option>
                                <flux:select.option value="positive_dark">{{ __('branding.colors.positive') }}</flux:select.option>
                                <flux:select.option value="negative_dark">{{ __('branding.colors.negative') }}</flux:select.option>
                                <flux:select.option value="storno_dark">{{ __('branding.colors.storno') }}</flux:select.option>
                                <flux:select.option value="accent_dark">{{ __('branding.colors.accent') }}</flux:select.option>
                                <flux:select.option value="accent_foreground_dark">{{ __('branding.colors.accent_foreground') }}</flux:select.option>
                                <flux:select.option value="accent_content_dark">{{ __('branding.colors.accent_content') }}</flux:select.option>
                            </flux:select>

                            @if($selectedDarkColor)
                                <div class="flex items-center gap-4">
                                    @if($selectedDarkColor === 'primary_dark')
                                        <flux:input type="color"
                                                    wire:model.live="form.primary_dark"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.primary_dark"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedDarkColor === 'secondary_dark')
                                        <flux:input type="color"
                                                    wire:model.live="form.secondary_dark"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.secondary_dark"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedDarkColor === 'brand_dark')
                                        <flux:input type="color"
                                                    wire:model.live="form.brand_dark"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.brand_dark"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedDarkColor === 'bg_dark')
                                        <flux:input type="color"
                                                    wire:model.live="form.bg_dark"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.bg_dark"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedDarkColor === 'text_dark')
                                        <flux:input type="color"
                                                    wire:model.live="form.text_dark"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.text_dark"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedDarkColor === 'positive_dark')
                                        <flux:input type="color"
                                                    wire:model.live="form.positive_dark"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.positive_dark"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedDarkColor === 'negative_dark')
                                        <flux:input type="color"
                                                    wire:model.live="form.negative_dark"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.negative_dark"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedDarkColor === 'storno_dark')
                                        <flux:input type="color"
                                                    wire:model.live="form.storno_dark"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.storno_dark"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedDarkColor === 'accent_dark')
                                        <flux:input type="color"
                                                    wire:model.live="form.accent_dark"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.accent_dark"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedDarkColor === 'accent_foreground_dark')
                                        <flux:input type="color"
                                                    wire:model.live="form.accent_foreground_dark"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.accent_foreground_dark"
                                                    placeholder="#000000"
                                        />
                                    @elseif($selectedDarkColor === 'accent_content_dark')
                                        <flux:input type="color"
                                                    wire:model.live="form.accent_content_dark"
                                                    class="w-24"
                                        />
                                        <flux:input wire:model.live="form.accent_content_dark"
                                                    placeholder="#000000"
                                        />
                                    @endif
                                </div>
                            @endif
                        </div>

                        <flux:separator/>

                        {{-- Mock Preview Dark --}}
                        <div class="space-y-3">
                            <flux:subheading>{{ __('branding.colors.preview') }}</flux:subheading>
                            <div class="border rounded-lg p-6 space-y-4"
                                 style="background-color: {{ $form->bg_dark }}; color: {{ $form->text_dark }};"
                            >

                                <div class="flex items-center justify-between pb-3 border-b"
                                     style="border-color: {{ $form->primary_dark }}40;"
                                >
                                    <h3 class="font-semibold"
                                        style="color: {{ $form->primary_dark }};"
                                    >
                                        {{ __('branding.colors.preview_heading') }}
                                    </h3>
                                    <span class="text-sm"
                                          style="color: {{ $form->secondary_dark }};"
                                    >
                                        {{ __('branding.colors.preview_secondary') }}
                                    </span>
                                </div>

                                <p class="text-sm"
                                   style="color: {{ $form->text_dark }};"
                                >
                                    {{ __('branding.colors.preview_body') }}
                                </p>

                                <div class="flex gap-2 flex-wrap">
                                    <button class="px-3 py-1.5 rounded text-sm font-medium"
                                            style="background-color: {{ $form->accent_dark }}; color: {{ $form->accent_foreground_dark }};"
                                    >
                                        {{ __('branding.colors.preview_accent_btn') }}
                                    </button>
                                    <button class="px-3 py-1.5 rounded text-sm font-medium"
                                            style="background-color: {{ $form->primary_dark }}; color: {{ $form->bg_dark }};"
                                    >
                                        {{ __('branding.colors.preview_primary_btn') }}
                                    </button>
                                    <button class="px-3 py-1.5 rounded text-sm"
                                            style="border: 1px solid {{ $form->secondary_dark }}; color: {{ $form->secondary_dark }};"
                                    >
                                        {{ __('branding.colors.preview_secondary_btn') }}
                                    </button>
                                </div>

                                <div class="flex gap-2 flex-wrap">
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->positive_dark }}30; color: {{ $form->positive_dark }};"
                                    >
                                        {{ __('branding.colors.preview_success') }}
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->negative_dark }}30; color: {{ $form->negative_dark }};"
                                    >
                                        {{ __('branding.colors.preview_error') }}
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->storno_dark }}30; color: {{ $form->storno_dark }};"
                                    >
                                        {{ __('branding.colors.preview_storno') }}
                                    </span>
                                    <span class="px-2 py-1 rounded text-xs font-medium"
                                          style="background-color: {{ $form->brand_dark }}30; color: {{ $form->brand_dark }};"
                                    >
                                        {{ __('branding.colors.preview_brand') }}
                                    </span>
                                </div>

                                <div class="border rounded p-4 space-y-2"
                                     style="border-color: {{ $form->primary_dark }}40; background-color: {{ $form->accent_dark }}20;"
                                >
                                    <h4 class="font-medium"
                                        style="color: {{ $form->accent_content_dark }};"
                                    >
                                        {{ __('branding.colors.preview_card_heading') }}
                                    </h4>
                                    <p class="text-sm"
                                       style="color: {{ $form->text_dark }};"
                                    >
                                        {{ __('branding.colors.preview_card_body') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </flux:card>
                </div>
            </div>

            <flux:button wire:click="saveColors"
                         variant="primary"
                         class="mt-6"
            >
                {{ __('branding.btn.save') }}
            </flux:button>
            <flux:button wire:click="restoreColors"
                         variant="ghost"
            >
                {{ __('branding.btn.restore') }}
            </flux:button>
        </flux:tab.panel>
        <flux:tab.panel name="org-logo"
                        label="{{ __('branding.tab_panel.logo') }}"
        >
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                <flux:callout class="col-span-full"
                              variant="warning"
                              icon="exclamation-triangle"
                              heading="{{ __('branding.logo.svgsanitizer_heading') }}"
                >
                    {{ __('branding.logo.svgsanitizer_text') }}
                </flux:callout>


                {{-- Logo Section --}}
                <flux:card class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('branding.logo.section_heading') }}</flux:heading>
                        <flux:subheading>{{ __('branding.logo.section_desc') }}</flux:subheading>
                    </div>

                    <flux:separator/>

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
                                    {{ __('branding.logo.current') }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if(app(\App\Services\SettingsService::class)->getLogo())
                                        {{ __('branding.logo.custom') }}
                                    @else
                                        {{ __('branding.logo.default') }}
                                    @endif
                                </p>
                            </div>

                            @if($showLogoUpload)
                                <div class="space-y-3">
                                    <flux:input
                                            type="file"
                                            wire:model="newLogo"
                                            accept="image/png,image/jpeg,image/jpg,image/svg+xml,image/webp"
                                            label="{{ __('branding.logo.upload_label') }}"
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
                                                {{ __('branding.logo.upload_btn') }}
                                            </flux:button>
                                            <flux:button
                                                    wire:click="$set('newLogo', null); $set('showLogoUpload', false)"
                                                    variant="ghost"
                                            >
                                                {{ __('branding.logo.cancel_btn') }}
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
                                        {{ __('branding.logo.new_btn') }}
                                    </flux:button>

                                    @if(app(\App\Services\SettingsService::class)->getLogo())
                                        <flux:button
                                                wire:click="resetLogo"
                                                wire:confirm="{{ __('branding.logo.reset_confirm') }}"
                                                variant="ghost"
                                        >
                                            {{ __('branding.logo.reset_btn') }}
                                        </flux:button>
                                    @endif
                                </div>
                            @endif

                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('branding.logo.hint') }}
                            </p>
                        </div>
                    </div>
                </flux:card>

                {{-- Favicon Section --}}
                <flux:card class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('branding.favicon.section_heading') }}</flux:heading>
                        <flux:subheading>{{ __('branding.favicon.section_desc') }}</flux:subheading>
                    </div>

                    <flux:separator/>

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
                                    {{ __('branding.favicon.current') }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if(app(\App\Services\SettingsService::class)->get('branding.favicon'))
                                        {{ __('branding.favicon.custom') }}
                                    @else
                                        {{ __('branding.favicon.default') }}
                                    @endif
                                </p>
                            </div>

                            @if($showFaviconUpload)
                                <div class="space-y-3">
                                    <flux:input
                                            type="file"
                                            wire:model="newFavicon"
                                            accept="image/png,image/x-icon,image/svg+xml,.ico"
                                            label="{{ __('branding.favicon.upload_label') }}"
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
                                                {{ __('branding.favicon.upload_btn') }}
                                            </flux:button>
                                            <flux:button
                                                    wire:click="$set('newFavicon', null); $set('showFaviconUpload', false)"
                                                    variant="ghost"
                                            >
                                                {{ __('branding.favicon.cancel_btn') }}
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
                                        {{ __('branding.favicon.new_btn') }}
                                    </flux:button>

                                    @if(app(\App\Services\SettingsService::class)->get('branding.favicon'))
                                        <flux:button
                                                wire:click="resetFavicon"
                                                wire:confirm="{{ __('branding.favicon.reset_confirm') }}"
                                                variant="ghost"
                                        >
                                            {{ __('branding.favicon.reset_btn') }}
                                        </flux:button>
                                    @endif
                                </div>
                            @endif

                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('branding.favicon.hint') }}
                            </p>
                        </div>
                    </div>
                </flux:card>
            </div>
        </flux:tab.panel>
        <flux:tab.panel name="org-info">
            <div class="space-y-6">
                {{-- Organization Section --}}
                <flux:card class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('branding.org.heading') }}</flux:heading>
                        <flux:subheading>{{ __('branding.org.subheading') }}</flux:subheading>
                    </div>

                    <flux:separator/>

                    <div class="grid gap-6 md:grid-cols-2">
                        <flux:fieldset label="{{ __('branding.org.heading') }}">
                            <flux:input
                                    wire:model="form.organization_name"
                                    label="{{ __('branding.org.name') }}"
                                    required
                            />

                            <flux:separator text="{{ __('branding.org.separator_address') }}"/>
                            <flux:input
                                    wire:model="form.organization_address"
                                    label="{{ __('branding.org.address') }}"
                                    placeholder="{{ __('branding.org.address_placeholder') }}"
                                    class="md:col-span-2"
                            />
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <flux:input
                                        wire:model="form.organization_zip"
                                        label="{{ __('branding.org.zip') }}"
                                        placeholder="{{ __('branding.org.zip_placeholder') }}"
                                        class="shrink-2"
                                />
                                <flux:input
                                        wire:model="form.organization_city"
                                        label="{{ __('branding.org.city') }}"
                                        placeholder="{{ __('branding.org.city_placeholder') }}"
                                        class="grow"
                                />
                            </div>
                            <flux:separator text="{{ __('branding.org.separator_communication') }}"/>
                            <flux:input
                                    wire:model="form.organization_email"
                                    label="{{ __('branding.org.email') }}"
                                    type="email"
                                    required
                            />

                            <flux:input
                                    wire:model="form.organization_web"
                                    label="{{ __('branding.org.website') }}"
                                    type="url"
                                    required
                                    class="md:col-span-2"
                            />
                        </flux:fieldset>

                        <flux:fieldset>
                            <flux:input wire:model="form.register_id"
                                        required
                                        label="{{ __('branding.org.register_id') }}"
                            />
                            <flux:date-picker locale="{{ app()->getLocale() }}" start-day="1"
                                              selectable-header
                                              locale="{{ app()->getLocale() }}"
                                              wire:model="form.registered_date"
                                              required
                                              label="{{ __('branding.org.registered_date') }}"
                            />
                            <flux:input wire:model="form.court"
                                        required
                                        label="{{ __('branding.org.court') }}"
                            />
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <flux:input wire:model="form.tax_id"
                                            label="{{ __('branding.org.tax_id') }}"
                                />
                                <flux:input wire:model="form.vat_id"
                                            label="{{ __('branding.org.vat_id') }}"
                                />
                            </div>
                        </flux:fieldset>
                    </div>
                </flux:card>
            </div>

            <flux:button wire:click="saveOrgInfo"
                         variant="primary"
                         class="mt-6"
            >
                {{ __('branding.btn.save') }}
            </flux:button>
            <flux:button wire:click="restoreOrgInfo"
                         variant="ghost"
            >
                {{ __('branding.btn.restore') }}
            </flux:button>
        </flux:tab.panel>
        <flux:tab.panel name="member-fees">
            <livewire:app.branding.fee-settings/>
        </flux:tab.panel>
        <flux:tab.panel name="org-statute">
            <flux:tab.group>
                <flux:tabs>
                    @foreach(\App\Models\Locale::getNames() as $locale)
                        <flux:tab name="org-statute-panel-{{$locale}}">{{ $locale }}</flux:tab>
                    @endforeach
                </flux:tabs>
                @foreach(\App\Models\Locale::getNames() as $locale)
                    <flux:tab.panel name="org-statute-panel-{{$locale}}">
                        <flux:editor wire:model="form.organization_statute.{{ $locale }}">
                            <flux:editor.toolbar>
                                <flux:editor.heading/>
                                <flux:editor.separator/>
                                <flux:editor.bold/>
                                <flux:editor.italic/>
                                <flux:editor.strike/>
                                <flux:editor.separator/>
                                <flux:editor.bullet/>
                                <flux:editor.ordered/>
                                <flux:editor.blockquote/>
                                <flux:editor.separator/>
                                <flux:editor.link/>
                                <flux:editor.separator/>
                                <flux:editor.align/>
                            </flux:editor.toolbar>
                            <flux:editor.content/>
                        </flux:editor>
                    </flux:tab.panel>
                @endforeach
            </flux:tab.group>

            <flux:button wire:click="saveStatute"
                         variant="primary"
                         class="mt-6"
            >
                {{ __('branding.btn.save') }}
            </flux:button>
            <flux:button wire:click="restoreStatute"
                         variant="ghost"
            >
                {{ __('branding.btn.restore') }}
            </flux:button>
        </flux:tab.panel>
        <flux:tab.panel name="org-texts">
            <flux:tab.group>
                <flux:tabs>
                    @foreach(\App\Models\Locale::getNames() as $locale)
                        <flux:tab name="org-text-panel-{{$locale}}">{{ $locale }}</flux:tab>
                    @endforeach
                </flux:tabs>
                @foreach(\App\Models\Locale::getNames() as $locale)
                    <flux:tab.panel name="org-text-panel-{{$locale}}">
                        <div class="space-y-6">
                            <flux:input
                                    wire:model="form.organization_slogan.{{ $locale }}"
                                    label="{{ __('branding.org.slogan', ['locale' => $locale]) }}"
                                    placeholder="{{ __('branding.org.slogan_placeholder') }}"
                                    class="md:col-span-2"
                            />

                            <flux:textarea
                                    wire:model="form.organization_description.{{ $locale }}"
                                    label="{{ __('branding.org.description') }}"
                                    rows="3"
                                    placeholder="{{ __('branding.org.description_placeholder') }}"
                                    class="md:col-span-2"
                            />
                            <section>
                                <flux:label>{{ __('branding.org.about_us_label') }}</flux:label>
                                <flux:editor wire:model="form.organization_about_us.{{ $locale }}">
                                    <flux:editor.toolbar>
                                        <flux:editor.heading/>
                                        <flux:editor.separator/>
                                        <flux:editor.bold/>
                                        <flux:editor.italic/>
                                        <flux:editor.strike/>
                                        <flux:editor.separator/>
                                        <flux:editor.bullet/>
                                        <flux:editor.ordered/>
                                        <flux:editor.blockquote/>
                                        <flux:editor.separator/>
                                        <flux:editor.link/>
                                        <flux:editor.separator/>
                                        <flux:editor.align/>
                                    </flux:editor.toolbar>
                                    <flux:editor.content/>
                                </flux:editor>
                            </section>
                        </div>
                    </flux:tab.panel>
                @endforeach
            </flux:tab.group>

            <flux:button wire:click="saveTexts"
                         variant="primary"
                         class="mt-6"
            >
                {{ __('branding.btn.save') }}
            </flux:button>
            <flux:button wire:click="restoreTexts"
                         variant="ghost"
            >
                {{ __('branding.btn.restore') }}
            </flux:button>
        </flux:tab.panel>

        <flux:tab.panel name="sepa">
            <div class="space-y-6">
                <flux:card class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('sepa.settings.creditor.heading') }}</flux:heading>
                        <flux:subheading>{{ __('sepa.settings.creditor.subheading') }}</flux:subheading>
                    </div>

                    <flux:separator/>

                    <flux:fieldset class="space-y-4">
                        <flux:input
                                wire:model="sepaForm.creditor_id"
                                label="{{ __('sepa.settings.creditor.creditor_id') }}"
                                placeholder="DE00ZZZ00000000000"
                                maxlength="35"
                                required
                        />

                        <flux:select
                                wire:model="sepaForm.creditor_account_id"
                                label="{{ __('sepa.settings.creditor.account') }}"
                                variant="listbox"
                                placeholder="{{ __('sepa.settings.creditor.account_placeholder') }}"
                        >
                            @foreach($sepaForm->bankAccounts() as $account)
                                <flux:select.option :value="$account['id']">
                                    {{ $account['label'] }}
                                    @if($account['missingIban'])
                                        <flux:icon.exclamation-triangle>{{ __('sepa.settings.iban_warnning') }}</flux:icon.exclamation-triangle>
                                    @endif
                                </flux:select.option>
                            @endforeach
                        </flux:select>

                        <div class="grid grid-cols-2 gap-4">
                            <flux:input
                                    wire:model="sepaForm.due_date_offset"
                                    label="{{ __('sepa.settings.creditor.due_date_offset') }}"
                                    type="number"
                                    min="1"
                                    max="30"
                                    required
                            />

                            <flux:select
                                    wire:model="sepaForm.pain_format"
                                    label="{{ __('sepa.settings.creditor.pain_format') }}"
                            >
                                <flux:select.option value="pain.008.001.02">pain.008.001.02</flux:select.option>
                                <flux:select.option value="pain.008.001.09">pain.008.001.09</flux:select.option>
                                <flux:select.option value="pain.008.003.01">pain.008.003.01</flux:select.option>
                            </flux:select>
                        </div>

                        <flux:select
                                wire:model.live="sepaForm.transfer_mode"
                                label="{{ __('sepa.settings.transfer.mode') }}"
                                variant="listbox"
                        >
                            <flux:select.option value="manual">{{ __('sepa.settings.transfer.mode_manual') }}</flux:select.option>
{{--      TODO: deaktivated until further implementation
                       <flux:select.option value="ebics">{{ __('sepa.settings.transfer.mode_ebics') }}</flux:select.option>
--}}
                        </flux:select>
                    </flux:fieldset>
                </flux:card>

                {{-- Info: Gläubiger-ID & PAIN-Formate --}}
                <flux:card>
                    <div class="space-y-4">
                        <div>
                            <flux:heading size="sm">{{ __('sepa.settings.info.heading') }}</flux:heading>
                        </div>

                        <flux:text class="text-sm">
                            <p><strong>{{ __('sepa.settings.info.creditor_id_label') }}</strong></p>
                            <p>{{ __('sepa.settings.info.creditor_id_text') }}</p>
                        </flux:text>

                        <flux:separator/>

                        <flux:text class="text-sm">
                            <p><strong>{{ __('sepa.settings.info.pain_formats_label') }}</strong></p>
                            <ul class="list-disc list-inside space-y-1 mt-1">
                                <li><code>pain.008.001.02</code> – {{ __('sepa.settings.info.pain_02') }}</li>
                                <li><code>pain.008.001.09</code> – {{ __('sepa.settings.info.pain_09') }}</li>
                                <li><code>pain.008.001.09</code> – {{ __('sepa.settings.info.pain_at') }}</li>
                                <li><code>pain.008.003.01</code> – {{ __('sepa.settings.info.pain_301') }}</li>
                            </ul>
                            <p class="mt-2">{{ __('sepa.settings.info.pain_recommendation') }}</p>
                        </flux:text>
                    </div>
                </flux:card>

    {{-- TODO: deaktivated until further implementation


             @if($sepaForm->transfer_mode === 'ebics')
                    <flux:card class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ __('sepa.settings.ebics.heading') }}</flux:heading>
                            <flux:subheading>{{ __('sepa.settings.ebics.subheading') }}</flux:subheading>
                        </div>

                        <flux:separator/>

                        <flux:fieldset class="space-y-6">
                            <flux:input
                                    wire:model="sepaForm.ebics_host"
                                    label="{{ __('sepa.settings.ebics.host') }}"
                                    placeholder="https://ebics.bank.de/ebics/ebics.aspx"
                            />

                            <div class="grid grid-cols-2 gap-4">
                                <flux:input
                                        wire:model="sepaForm.ebics_host_id"
                                        label="{{ __('sepa.settings.ebics.host_id') }}"
                                        placeholder="BANKDEFFXXX"
                                />

                                <flux:input
                                        wire:model="sepaForm.ebics_partner_id"
                                        label="{{ __('sepa.settings.ebics.partner_id') }}"
                                        placeholder="12345"
                                />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <flux:input
                                        wire:model="sepaForm.ebics_user_id"
                                        label="{{ __('sepa.settings.ebics.user_id') }}"
                                        placeholder="U12345"
                                />

                                <flux:input
                                        wire:model="sepaForm.ebics_passphrase"
                                        label="{{ __('sepa.settings.ebics.passphrase') }}"
                                        type="password"
                                />
                            </div>
                        </flux:fieldset>
                    </flux:card>
                @endif--}}

                <flux:button wire:click="saveSepa"
                             variant="primary"
                             class="mt-6"
                >
                    {{ __('sepa.settings.btn.save') }}
                </flux:button>
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="datev">
            <div class="space-y-6">
                <flux:card class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('accounting.datev.settings.heading') }}</flux:heading>
                        <flux:subheading>{{ __('accounting.datev.settings.subheading') }}</flux:subheading>
                    </div>

                    @unless(app(\App\Services\Accounting\DatevSettingsService::class)->isConfigured())
                        <flux:callout icon="exclamation-triangle"
                                      variant="warning"
                        >
                            <flux:callout.heading>{{ __('accounting.datev.settings.not_configured_heading') }}</flux:callout.heading>
                            <flux:callout.text>{{ __('accounting.datev.settings.not_configured_text') }}</flux:callout.text>
                        </flux:callout>
                    @endunless

                    <flux:separator/>

                    <flux:fieldset class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input
                                    wire:model="datevForm.berater_nr"
                                    label="{{ __('accounting.datev.settings.berater_nr') }}"
                                    description="{{ __('accounting.datev.settings.berater_nr_description') }}"
                                    placeholder="1001"
                                    maxlength="7"
                                    required
                            />

                            <flux:input
                                    wire:model="datevForm.mandant_nr"
                                    label="{{ __('accounting.datev.settings.mandant_nr') }}"
                                    description="{{ __('accounting.datev.settings.mandant_nr_description') }}"
                                    placeholder="10000"
                                    maxlength="5"
                                    required
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <flux:select
                                    wire:model.live="datevForm.skr"
                                    label="{{ __('accounting.datev.settings.skr') }}"
                                    description="{{ __('accounting.datev.settings.skr_description') }}"
                            >
                                {{-- Aktuell wird nur SKR42 unterstützt (Seeder + Geldkonto-Mapping sind SKR42-basiert) --}}
                                <flux:select.option value="42">SKR42 – {{ __('accounting.datev.settings.skr_42') }}</flux:select.option>
                            </flux:select>

                            <flux:input
                                    wire:model="datevForm.konto_laenge"
                                    label="{{ __('accounting.datev.settings.konto_laenge') }}"
                                    description="{{ __('accounting.datev.settings.konto_laenge_description') }}"
                                    type="number"
                                    readonly
                            />
                        </div>

                        <flux:input
                                wire:model="datevForm.application_info"
                                label="{{ __('accounting.datev.settings.application_info') }}"
                                description="{{ __('accounting.datev.settings.application_info_description') }}"
                                maxlength="25"
                        />
                    </flux:fieldset>
                </flux:card>

                <flux:card>
                    <div class="space-y-4">
                        <flux:heading size="sm">{{ __('accounting.datev.settings.info.heading') }}</flux:heading>

                        <flux:text class="text-sm">
                            <p>{{ __('accounting.datev.settings.info.numbers_text') }}</p>
                            <p class="mt-2">{{ __('accounting.datev.settings.info.validation_text') }}</p>
                        </flux:text>
                    </div>
                </flux:card>

                <flux:button wire:click="saveDatev"
                             variant="primary"
                             class="mt-6"
                >
                    {{ __('accounting.datev.settings.btn.save') }}
                </flux:button>
            </div>
        </flux:tab.panel>

        <flux:tab.panel name="locales">

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                <nav class="lg:col-span-1 space-y-3">
                    <flux:heading size="lg">{{ __('branding.locales.heading') }}</flux:heading>
                    <flux:navlist>
                        @foreach($this->locales as $locale)
                            <flux:navlist.item wire:click="editLocale({{ $locale->id }})"
                                               icon="language"
                            >
                                <div class="flex justify-between items-center">
                                    <span>{{ $locale->label }}</span>
                                    @if($locale->active)
                                        <flux:badge color="lime"
                                                    size="sm"
                                        >{{ __('branding.locales.active') }}</flux:badge>
                                    @else
                                        <flux:badge size="sm">{{ __('branding.locales.inactive') }}</flux:badge>
                                    @endif
                                </div>
                            </flux:navlist.item>
                        @endforeach
                        <flux:navlist.item wire:click="createLocale"
                                           icon="plus"
                        >{{ __('branding.locales.new') }}</flux:navlist.item>
                    </flux:navlist>
                </nav>
                <flux:card class="lg:col-span-3 space-y-6">
                    <flux:field variant="inline">
                        <flux:checkbox wire:model="localeForm.active"/>
                        <flux:label>{{ __('branding.locales.active_label') }}</flux:label>
                        <flux:error name="localeForm.active"/>
                    </flux:field>
                    <flux:input wire:model="localeForm.label"
                                label="{{ __('branding.locales.label') }}"
                    />
                    <flux:input wire:model="localeForm.name"
                                label="{{ __('branding.locales.name') }}"
                    />
                    <flux:input wire:model="localeForm.decimal_separator"
                                label="{{ __('branding.locales.decimal_separator') }}"
                    />
                    <flux:input wire:model="localeForm.thousands_separator"
                                label="{{ __('branding.locales.thousands_separator') }}"
                    />
                    <flux:input wire:model="localeForm.currency_symbol"
                                label="{{ __('branding.locales.currency_symbol') }}"
                    />
                    <flux:radio.group wire:model="localeForm.currency_position"
                                      label="{{ __('branding.locales.currency_position') }}"
                    >
                        <flux:radio value="before"
                                    label="{{ __('branding.locales.currency_before') }}"
                        />
                        <flux:radio value="after"
                                    label="{{ __('branding.locales.currency_after') }}"
                        />
                    </flux:radio.group>
                    <flux:radio.group wire:model="localeForm.name_order"
                                      label="{{ __('branding.locales.name_order') }}"
                    >
                        <flux:radio value="first_last"
                                    label="{{ __('branding.locales.name_order_first_last') }}"
                        />
                        <flux:radio value="last_first"
                                    label="{{ __('branding.locales.name_order_last_first') }}"
                        />
                    </flux:radio.group>
                    <flux:input wire:model="localeForm.date_format"
                                label="{{ __('branding.locales.date_format') }}"
                    />

                    <aside>
                        <flux:button wire:click="storeLocale"
                                     variant="primary"
                                     size="sm"
                        >{{ __('branding.locales.save_btn') }}</flux:button>
                        @if(isset($localeForm->id))
                            <flux:modal.trigger name="delete-locale">
                                <flux:button variant="danger"
                                             size="sm"
                                >{{ __('branding.locales.delete_btn') }}</flux:button>
                            </flux:modal.trigger>

                            <flux:modal name="delete-locale"
                                        class="min-w-[22rem]"
                            >
                                <div class="space-y-6">
                                    <div>
                                        <flux:heading size="lg">{{ __('branding.locales.delete_heading') }}</flux:heading>

                                        <flux:text class="mt-2">
                                            {{ __('branding.locales.delete_text') }}
                                        </flux:text>
                                    </div>

                                    <div class="flex gap-2">
                                        <flux:spacer/>

                                        <flux:modal.close>
                                            <flux:button variant="ghost"
                                                         size="sm"
                                            >{{ __('branding.locales.delete_cancel') }}</flux:button>
                                        </flux:modal.close>

                                        <flux:button wire:click="deleteLocale"
                                                     variant="danger"
                                                     size="sm"
                                        >{{ __('branding.locales.delete_confirm') }}</flux:button>
                                    </div>
                                </div>
                            </flux:modal>
                        @endif
                    </aside>

                </flux:card>
            </div>
        </flux:tab.panel>

    </flux:tab.group>

</div>
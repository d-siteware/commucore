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

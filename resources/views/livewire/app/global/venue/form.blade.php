<div>
    <flux:heading size="lg" class="mb-6">
        {{ $isEditing ? __('venue.edit.heading') : __('venue.new.heading') }}
    </flux:heading>

    <form wire:submit="save" class="space-y-6">
        <section class="space-y-6" x-data="{ showGeoText: false }">
            <flux:input wire:model="form.name"
                        label="{{ __('venue.name') }}"
            />
            <flux:input wire:model="form.address"
                        label="{{ __('venue.address') }}"
            />
            <flux:input wire:model="form.postal_code"
                        label="{{ __('venue.postal_code') }}"
            />
            <flux:input wire:model="form.city"
                        label="{{ __('venue.city') }}"
            />
            <flux:input wire:model="form.country"
                        label="{{ __('venue.country') }}"
            />
            <flux:input wire:model="form.phone"
                        label="{{ __('venue.phone') }}"
            />
            <flux:input wire:model="form.website"
                        label="{{ __('venue.website') }}"
            />
            <flux:field>
                <flux:label>
                    {{ __('venue.geolocation') }}
                    <flux:button size="xs" variant="ghost" type="button" @click="showGeoText = !showGeoText">
                        {{ __('venue.geolocation.more') }}
                    </flux:button>
                </flux:label>
                <flux:input wire:model="form.geolocation"/>
            </flux:field>

            <aside class="p-2 border rounded-md w-72" x-cloak x-show="showGeoText">
                <flux:heading>Google Plus Code</flux:heading>
                <p class="text-sm">{{ __('venue.geolocation.hint') }}</p>
            </aside>
        </section>

        <footer class="flex pt-3 justify-between items-center">
            <flux:button type="button"
                         size="sm"
                         wire:click="saveOnly"
            >
                {{ __('venue.form.save_only') }}
            </flux:button>
            <flux:button type="submit"
                         size="sm"
                         variant="primary"
            >
                {{ __('venue.form.save_and_apply') }}
            </flux:button>
        </footer>
    </form>
</div>
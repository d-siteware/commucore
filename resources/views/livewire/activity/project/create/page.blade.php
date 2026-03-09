<div>
    <flux:heading size="xl" class="mb-3 lg:mb-9">{{ __('projects.create.page.title') }}</flux:heading>

    <form wire:submit="createProject">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

            <section class="space-y-6">

                <flux:input wire:model="form.title"
                            label="{{ __('projects.form.title') }}"
                />

                <flux:textarea wire:model="form.description"
                               rows="auto"
                               label="{{ __('projects.form.description') }}"
                />

                <flux:select wire:model="form.status"
                             variant="listbox"
                             label="{{ __('projects.form.status') }}"
                >
                    @foreach(\App\Enums\ProjectStatus::cases() as $s)
                        <flux:select.option value="{{ $s->value }}">
                            <flux:badge color="{{ $s->color() }}">{{ $s->label() }}</flux:badge>
                        </flux:select.option>
                    @endforeach
                </flux:select>

            </section>

            <section class="space-y-6">

                <flux:date-picker wire:model="form.start_date"
                                  with-today
                                  selectable-header
                                  label="{{ __('projects.form.start_date') }}"
                />

                <flux:date-picker wire:model="form.end_date"
                                  with-today
                                  selectable-header
                                  label="{{ __('projects.form.end_date') }}"
                />

            </section>

        </div>

        <div class="mt-6 flex justify-between">
            <flux:button href="{{ route('project.index') }}"
                         wire:navigate
                         variant="ghost"
            >{{ __('app.btn.cancel') }}</flux:button>

            <flux:button type="submit" variant="primary">
                {{ __('projects.create.btn.submit') }}
            </flux:button>
        </div>
    </form>

    @if(!app()->isProduction())
        <x-debug/>
    @endif
</div>
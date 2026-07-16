<div class="space-y-6">

    <flux:heading size="lg">
        {{ $isEditing ? __('fundings.link_project.heading.edit') : __('fundings.link_project.heading.new') }}
    </flux:heading>

    <form wire:submit="{{ $isEditing ? 'updatePivot' : 'attach' }}">
        <div class="space-y-6">

            @if(!$isEditing)
                <flux:select wire:model.blur="project_id"
                             variant="listbox"
                             searchable
                             label="{{ __('fundings.link_project.form.project') }}"
                             placeholder="{{ __('fundings.link_project.form.project_placeholder') }}"
                >
                    @foreach($availableProjects as $project)
                        <flux:select.option value="{{ $project->id }}">
                            <div class="flex items-center gap-2">
                                {{ $project->title }}
                                <flux:badge size="sm" color="{{ $project->status->color() }}">
                                    {{ $project->status->label() }}
                                </flux:badge>
                            </div>
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="project_id"/>
            @else
                <flux:text class="text-sm text-gray-500">
                    {{ __('fundings.link_project.form.editing_hint') }}
                </flux:text>
            @endif

            <flux:field>
                <flux:label>{{ __('fundings.link_project.form.allocated_amount') }}</flux:label>
                <flux:description>{{ __('fundings.link_project.form.allocated_amount_hint') }}</flux:description>
                <flux:input.group>
                    <flux:input wire:model.blur="allocated_amount"
                                placeholder="0,00"
                                x-mask:dynamic="$money($input, ',', '.')"
                    />
                    <flux:input.group.suffix>{{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</flux:input.group.suffix>
                </flux:input.group>
                <flux:error name="allocated_amount"/>
            </flux:field>

            {{-- Remaining budget hint --}}
            <div class="text-sm text-gray-500 bg-gray-50 dark:bg-zinc-900 rounded p-3">
                {{ __('fundings.link_project.form.remaining_hint') }}:
                <span class="font-semibold text-blue-600">
                    {{ \App\Helpers\MoneyHelper::formatCents($funding->remainingAmount()) }}
                </span>
            </div>

            <div class="flex gap-3">
                <flux:button type="submit" variant="primary" class="flex-1">
                    {{ $isEditing ? __('fundings.link_project.form.btn.update') : __('fundings.link_project.form.btn.attach') }}
                </flux:button>

                @if($isEditing)
                    <flux:button type="button"
                                 variant="ghost"
                                 wire:click="$set('isEditing', false)"
                    >{{ __('app.btn.cancel') }}</flux:button>
                @endif
            </div>

        </div>
    </form>

</div>
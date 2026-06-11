<div>
    <flux:card class="space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
            <div>
                <flux:heading size="sm"
                              class="text-zinc-500 dark:text-zinc-400 uppercase tracking-wide text-xs font-semibold"
                >
                    {{ __('dashboard.member_growth_heading') }}
                </flux:heading>
                <div class="flex items-baseline gap-2 mt-1">
                    <flux:heading size="xl"
                                  class="tabular-nums"
                    >
                        {{ number_format(collect($data)->last()['members'] ?? 0) }}
                    </flux:heading>
                    <flux:text class="text-zinc-500 dark:text-zinc-400 text-sm">{{ __('dashboard.active_members_label') }}</flux:text>
                </div>
            </div>
            {{-- Zeitraum-Umschalter --}}
            <flux:select size="sm" class="w-28">
                @foreach (['week' => 'Woche', 'month' => 'Monat', 'year' => 'Jahr', 'all' => 'Gesamt'] as $value => $label)
                <flux:select.option wire:click="$set('period', '{{ $value }}')">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>
             {{--   <flux:button.group>
                @foreach (['week' => __('dashboard.period_week'), 'month' => __('dashboard.period_month'), 'year' => __('dashboard.period_year'), 'all' => __('dashboard.period_all')] as $value => $label)
                        <flux:button
                                wire:click="$set('period', '{{ $value }}')"
                                size="xs"
                                variant="{{ $period === $value ? 'filled' : 'ghost' }}"
                        >
                            {{ $label }}
                        </flux:button>
                    @endforeach
                </flux:button.group>--}}

        </div>

        <flux:chart wire:model="data"
                    class="aspect-3/1"
        >
            <flux:chart.svg>
                <flux:chart.line field="members"
                                 class="text-emerald-500 dark:text-emerald-400"
                />

                <flux:chart.axis axis="x"
                                 field="date"
                >
                    <flux:chart.axis.line/>
                    <flux:chart.axis.tick/>
                </flux:chart.axis>

                <flux:chart.axis axis="y">
                    <flux:chart.axis.grid/>
                    <flux:chart.axis.tick/>
                </flux:chart.axis>

                <flux:chart.cursor/>
            </flux:chart.svg>

            <flux:chart.tooltip>
                <flux:chart.tooltip.heading field="date"/>
                <flux:chart.tooltip.value field="members"
                                          :label="__('dashboard.members_chart_label')"
                />
            </flux:chart.tooltip>
        </flux:chart>
    </flux:card>
</div>

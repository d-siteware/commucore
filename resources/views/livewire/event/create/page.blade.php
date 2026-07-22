<div>
@dump($step)
    <flux:heading size="lg"
                  class="mb-3 lg:mb-6"
    >{{ __('event.create.page.title') }}</flux:heading>
    <!-- Progress Bar -->
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
                                       label="{{ __('event.create.step.core_data') }}"
                    />
                @elseif($step === 1)
                    <x-steps.current :item="1"
                                     step="01"
                                     label="{{ __('event.create.step.core_data') }}"
                    />
                @else
                    <x-steps.upcomming :item="1"
                                       step="01"
                                       label="{{ __('event.create.step.core_data') }}"
                    />
                @endif
            </li>
            <li class="relative md:flex md:flex-1">
                @if($step > 2)
                    <x-steps.completed :item="2"
                                       step="02"
                                       label="{{ __('event.create.step.texts') }}"
                    />
                @elseif($step === 2)
                    <x-steps.current :item="2"
                                     step="02"
                                     label="{{ __('event.create.step.texts') }}"
                    />
                @else
                    <x-steps.upcomming :item="2"
                                       step="02"
                                       label="{{ __('event.create.step.texts') }}"
                    />
                @endif
            </li>
            <li class="relative md:flex md:flex-1">
                @if($step > 3)
                    <x-steps.completed :item="3"
                                       step="03"
                                       label="{{ __('event.create.step.cover_image') }}"
                                       last
                    />
                @elseif($step === 3)
                    <x-steps.current :item="3"
                                     step="03"
                                     label="{{ __('event.create.step.cover_image') }}"
                                     last

                    />
                @else
                    <x-steps.upcomming :item="3"
                                       step="03"
                                       label="{{ __('event.create.step.cover_image') }}"
                                       last="true"

                    />
                @endif
            </li>
        </ol>
    </nav>

    <form wire:submit="createEventData"

    >
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3">
            @if ($step == 1)
                <flux:card class="col-span-1">

                    <flux:field>
                        <flux:label badge="{{ __('app.form.field.required') }}">{{ __('event.form.name') }}</flux:label>
                        <flux:input wire:model.blur="form.name"
                                    class="mb-3"
                        />
                        <flux:error name="form.name"/>
                    </flux:field>


                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
                        <flux:fieldset class="space-y-6">

                            <flux:field>
                                <flux:label badge="{{ __('app.form.field.required') }}">{{ __('event.form.event_date') }}</flux:label>
                                <flux:date-picker locale="{{ app()->getLocale() }}" wire:model.blur="form.event_date"
                                                  with-today
                                                  selectable-header
                                                  fixed-weeks
                                />
                                <flux:error name="form.event_date"/>
                            </flux:field>


                            <flux:field>
                                <flux:label badge="{{ __('app.form.field.required') }}">{{ __('event.form.start_time') }}</flux:label>
                                <flux:input type="time"
                                            wire:model.blur="form.start_time"
                                />
                                <flux:error name="form.start_time"/>
                            </flux:field>
                            <flux:field>
                                <flux:label badge="{{ __('app.form.field.required') }}">{{ __('event.form.end_time') }}</flux:label>
                                <flux:input type="time"
                                            wire:model.blur="form.end_time"
                                />
                                <flux:error name="form.end_time"/>
                            </flux:field>

                        </flux:fieldset>

                        <flux:fieldset class="space-y-6">
                            <flux:field>
                                <flux:label>{{__('event.form.entry_fee')}}</flux:label>
                                <flux:input.group>
                                    <flux:input type="number"
                                                wire:model.blur="form.entry_fee"
                                    />
                                    <flux:input.group.suffix>{{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</flux:input.group.suffix>
                                </flux:input.group>
                                <flux:error name="entry_fee"/>
                            </flux:field>
                            <flux:field>
                                <flux:label>{{__('event.form.entry_fee_discounted')}}</flux:label>
                                <flux:input.group>
                                    <flux:input type="number"
                                                wire:model.blur="form.entry_fee_discounted"
                                    />
                                    <flux:input.group.suffix>{{ \App\Helpers\MoneyHelper::getCurrencySymbol() }}</flux:input.group.suffix>
                                </flux:input.group>
                                <flux:error name="entry_fee_discounted"/>
                            </flux:field>

                            <flux:input wire:model.blur="form.payment_link"
                                        label="{{ __('event.form.payment_link') }}"
                            />
                        </flux:fieldset>

                        <section class="space-y-6">
                            <flex:field class="space-y-2">
                                <flux:label>{{__('event.form.venue_id')}}</flux:label>

                                <flux:select variant="listbox"
                                             searchable
                                             placeholder="{{ __('event.form.venue.select') }}"
                                             wire:model.blur="form.venue_id"
                                >
                                    @foreach($this->venues as $key => $venue)
                                        <flux:select.option value="{{ $venue->id }}"
                                                            :key
                                        >{{ $venue->name }}</flux:select.option>
                                    @endforeach

                                </flux:select>

                                <div class="pt-3">
                                    <flux:button size="sm"
                                                 variant="ghost"
                                                 wire:click="$dispatch('open-venue-create')"
                                    >
                                        {{ __('venue.new.btn.label') }}
                                    </flux:button>
                                </div>
                            </flex:field>

                            <flux:field>
                                <flux:label>{{__('event.type.label')}}</flux:label>
                                <flux:select variant="listbox"
                                             placeholder="{{ __('event.type.label') }}"
                                             wire:model.blur="form.type"
                                >
                                    @foreach(App\Enums\EventType::cases() as $type)
                                        <flux:select.option value="{{ $type->value }}"
                                                            wire:key="type-{{ $type->value }}"
                                        >{{ $type->label() }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                            </flux:field>

                        </section>

                    </div>

                </flux:card>
            @endif

            @if($step===2)
                <section class="col-span-2">
                    <flux:button size="sm"
                                 wire:click="makeWebText"
                                 variant="primary"
                                 icon-trailing="document"
                    >{{ __('event.backend.text-nav.btn-make-web-texts') }}</flux:button>

                    @isMultiLang()
                    <flux:tab.group>
                        <flux:tabs>
                            @foreach(\App\Models\Locale::getNames() as $locale)
                                <flux:tab name="event-text-{{ $locale}}">{{ $locale}}</flux:tab>
                                @if($errors->hasAny(["form.title.{$locale}", "form.slug.{$locale}"]))
                                    <flux:badge color="red" size="sm">!</flux:badge>
                                @endif
                            @endforeach
                        </flux:tabs>
                        @foreach(\App\Models\Locale::getNames()  as $locale)
                            <flux:tab.panel name="event-text-{{ $locale }}">
                                <x-events.event-texts :locale="$locale"/>
                            </flux:tab.panel>
                        @endforeach
                    </flux:tab.group>

                    @else
                        <x-events.event-texts :locale="app()->getLocale()" :multi-lang="false"/>
                    @endIsMultiLang
                </section>
            @endif
            @if($step===3)
                <flux:card class="space-y-3">
                    <flux:heading>{{ __('event.form.image.upload') }}</flux:heading>

                    <livewire:app.global.image-upload/>
                </flux:card>
            @endif

        </section>


        <div class="mt-6 flex justify-between">
            @if ($step > 1)
                <flux:button type="button"
                             wire:click="previousStep"
                             variant="filled"
                >{!!  __('pagination.previous') !!}</flux:button>
            @else
                <span></span>
            @endif
            @if ($step < $totalSteps)
                <flux:button type="button"
                             wire:click="nextStep"
                             variant="primary"
                >{!! __('pagination.next') !!}</flux:button>
            @else
                <flux:button type="submit"
                             variant="primary"
                >{{ __('event.form.btn.save') }}
                </flux:button>
            @endif
        </div>
    </form>

    @if(!app()->isProduction())

        <x-debug/>

        <flux:button wire:click="addDemoData"
                     variant="ghost"
        >
        </flux:button>

    @endif

    <livewire:app.global.venue.modal />

</div>

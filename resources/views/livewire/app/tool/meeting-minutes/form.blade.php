<div>
    <section class="grid grid-cols-1 gap-6 s lg:grid-cols-3 2xl:gap-8">
        <flux:card class="space-y-6 mt-3 lg:mt-6 lg:col-span-2">
            <form wire:submit="save"
                  class="grid grid-cols-1 gap-3 lg:grid-cols-2 lg:gap-6"
            >
                <flux:date-picker label="{{ __('minutes.create.meeting_date') }}"
                                  wire:model="minuteForm.meeting_date"
                                  placeholder="{{ __('minutes.create.meeting_date_placeholder') }}"
                                  with-today
                                  selectable-header
                                  start-day="1"
                                  fixed-weeks
                />
                @error('minuteForm.meeting_date')
                <flux:text class="text-red-500">{{ $message }}</flux:text> @enderror
                <flux:input label="{{ __('minutes.create.location') }}"
                            wire:model="minuteForm.location"
                />
                @error('minuteForm.location')
                <flux:text class="text-red-500">{{ $message }}</flux:text> @enderror
                <flux:input label="{{ __('minutes.create.title') }}"
                            wire:model="minuteForm.title"
                            required
                />
                @error('minuteForm.title')
                <flux:text class="text-red-500">{{ $message }}</flux:text> @enderror
                <flux:textarea wire:model="minuteForm.content"
                               label="{{ __('minutes.create.content') }}"
                />
                @error('minuteForm.content')
                <flux:text class="text-red-500">{{ $message }}</flux:text> @enderror
                <flux:button type="submit"
                             variant="primary"
                             class="mt-4"
                >
                    {{ __('minutes.create.save') }}
                </flux:button>
            </form>
        </flux:card>

        <flux:card class="space-y-6 mt-3 lg:mt-6">
            <section class="flex gap-3 flex-col ">
                <div class="flex gap-3 mr-3 items-center">
                    <flux:heading size="lg">{{ __('minutes.create.attendees.heading') }}</flux:heading>
                    <flux:modal.trigger name="add-attendee">
                        <flux:button size="sm">{{ __('minutes.create.btn.add_attendee') }}</flux:button>
                    </flux:modal.trigger>
                </div>

                <flux:separator/>
                <div class="flex gap-3 flex-wrap">
                    @forelse($attendeesList as $key => $attendee)
                        <figure class="inline-flex items-center gap-x-0.5 rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-400/10 dark:text-gray-400"  wire:key="attendee-{{ $key }}">
                            {{ $attendee['name'] }}
                          <button type="button" wire:click="removeAttendee({{ $key }})"
                                  class="group relative -mr-1 size-3.5 rounded-xs hover:bg-gray-500/20 dark:hover:bg-gray-400/20"
                          >
                            <span class="sr-only">Remove</span>
                            <svg viewBox="0 0 14 14"
                                 class="size-3.5 stroke-gray-700/50 group-hover:stroke-gray-700/75 dark:stroke-gray-400 dark:group-hover:stroke-gray-300"
                            >
                              <path d="M4 4l6 6m0-6l-6 6"/>
                            </svg>
                            <span class="absolute -inset-1"></span>
                          </button>
                        </figure>

                    @empty
                        <flux:text>{{ __('minutes.create.empty_attendee_list') }}</flux:text>
                    @endforelse
                </div>
            </section>
            @error('attendeesList')
            <flux:text class="text-red-500">{{ $message }}</flux:text> @enderror
            @error('attendeesList.*.name')
            <flux:text class="text-red-500">{{ $message }}</flux:text> @enderror
        </flux:card>
    </section>

    <flux:separator text="{{ __('minutes.create.topic.heading') }}"
                    class="my-6 lg:my-9"
    />

    <section>
        <nav class="flex justify-between items-center my-3 lg:my-6">
            <flux:heading size="lg">{{ __('minutes.create.topic.heading') }}</flux:heading>
            <flux:button variant="primary"
                         size="sm"
                         wire:click="addTopic"
            >
                {{ __('minutes.create.topic.add') }}
            </flux:button>
        </nav>
        @forelse($topics as $index => $topic)
            <flux:card class="space-y-6"
                       wire:key="topic-{{ $topic['temporary_id'] }}"
            >
                <flux:button size="xs"
                             variant="ghost"
                             wire:click="removeTopic({{ $index }})"
                >
                    {{ __('minutes.create.topic.remove') }}
                </flux:button>
                <div class="grid grid-cols-1 lg:grid-cols-6 gap-3">
                    <flux:editor wire:model.live.debounce.500ms="topicsList.{{ $index }}.content"
                                 class="col-span-6 lg:col-span-4"
                                 placeholder="{{ __('minutes.create.topic.placeholder') }}"
                    />
                    @error('topicsList.{{ $index }}.content')
                    <flux:text class="text-red-500">{{ $message }}</flux:text> @enderror
                    <aside class="col-span-6 lg:col-span-2">
                        <nav class="flex justify-between items-center mb-3 lg:mb-6">
                            <flux:heading size="lg">{{ __('minutes.create.actionitems.heading') }}</flux:heading>
                            <flux:modal.trigger name="add-action-item"
                                                wire:click="openActionItemModal({{ $index }})"
                            >
                                <flux:button variant="primary"
                                             size="sm"
                                >
                                    {{ __('minutes.create.actionitems.add') }}
                                </flux:button>
                            </flux:modal.trigger>
                        </nav>
                        @forelse($actionItems as $actionIndex => $actionItem)
                            @if($actionItem['topic_temporary_id'] === $topic['temporary_id'])
                                <div class="flex justify-between items-center"
                                     wire:key="action-item-{{ $actionItem['temporary_id'] }}"
                                >
                                    <flux:text>{{ $actionItem['description'] }}</flux:text>
                                    <div class="flex items-center gap-2">
                                        <flux:text>{{ $members->find($actionItem['assignee_id'])?->name ?? __('minutes.create.actionitems.no_assignee') }}</flux:text>
                                        <flux:text>{{ $actionItem['due_date'] ? \Carbon\Carbon::parse($actionItem['due_date'])->format('Y-m-d') : '-' }}</flux:text>
                                        <flux:button size="xs"
                                                     icon-trailing="x-mark"
                                                     variant="ghost"
                                                     wire:click="removeActionItem({{ $actionIndex }})"
                                        >
                                            {{ __('minutes.create.actionitems.remove') }}
                                        </flux:button>
                                    </div>
                                </div>
                                @error('actionItems.{{ $actionIndex }}.description')
                                <flux:text class="text-red-500">{{ $message }}</flux:text> @enderror
                            @endif
                        @empty
                            <flux:text>{{ __('minutes.create.actionitems.empty') }}</flux:text>
                        @endforelse
                    </aside>
                </div>

            </flux:card>
        @empty
            <flux:text>{{ __('minutes.create.topic.empty_topics_list') }}</flux:text>
        @endforelse
        @error('topicsList')
        <flux:text class="text-red-500">{{ $message }}</flux:text> @enderror
    </section>

    <flux:modal name="add-attendee"
                variant="flyout"
                position="right"
    >
        <header class="my-9">
            <flux:heading size="lg">{{ __('minutes.create.modal.add_attendee.header') }}</flux:heading>
        </header>
        <section class="space-y-6">
            <flux:select wire:model.live="newAttendeeMemberId"
                         variant="listbox"
                         searchable
                         placeholder="{{ __('minutes.create.modal.add_attendee.select_member') }}"
            >
                <flux:select.option value="0">{{ __('minutes.create.modal.add_attendee.no_member') }}</flux:select.option>
                @foreach($members as $member)
                    <flux:select.option value="{{ $member->id }}"
                                        wire:key="member-{{ $member->id }}"
                    >
                        {{ trim("{$member->first_name} {$member->name}") }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:button wire:click="addBoardMembers"
                         variant="filled"
            >{{ __('minutes.create.modal.add_attendee.add_board') }}</flux:button>

            <flux:input wire:model="newAttendeeName"
                        label="{{ __('minutes.create.modal.add_attendee.name') }}"
                        required
            />
            <flux:input wire:model="newAttendeeEmail"
                        type="email"
                        label="{{ __('minutes.create.modal.add_attendee.email') }}"
            />
            <flux:button variant="primary"
                         wire:click="addAttendee"
            >
                {{ __('minutes.create.modal.add_attendee.btn') }}
            </flux:button>
        </section>
    </flux:modal>

    <flux:modal name="add-action-item"
                variant="flyout"
                position="right"
    >
        <header class="my-9">
            <flux:heading size="lg">{{ __('minutes.create.modal.add_action_item.header') }}</flux:heading>
        </header>
        <section class="space-y-6">
            <flux:input wire:model.live="newActionItemDescription"
                        label="{{ __('minutes.create.modal.add_action_item.description') }}"
                        required
            />
            @error('newActionItemDescription')
            <flux:text class="text-red-500">{{ $message }}</flux:text> @enderror
            <flux:select variant="listbox"
                         searchable
                         placeholder="{{ __('minutes.create.modal.add_action_item.select_assignee') }}"
                         wire:model.live="newActionItemAssigneeId"
            >
                <flux:select.option value="">{{ __('minutes.create.modal.add_action_item.no_assignee') }}</flux:select.option>
                @foreach($members as $member)
                    <flux:select.option wire:key="assignee-{{ $member->id }}"
                                        value="{{ $member->id }}"
                    >
                        {{ trim("{$member->first_name} {$member->name}") }}
                    </flux:select.option>
                @endforeach
            </flux:select>
            @error('newActionItemAssigneeId')
            <flux:text class="text-red-500">{{ $message }}</flux:text> @enderror
            <flux:date-picker wire:model.live="newActionItemDueDate"
                              label="{{ __('minutes.create.modal.add_action_item.due_date') }}"
                              placeholder="{{ __('minutes.create.modal.add_action_item.due_date_placeholder') }}"
                              with-today
                              selectable-header
                              fixed-weeks
            />
            @error('newActionItemDueDate')
            <flux:text class="text-red-500">{{ $message }}</flux:text> @enderror
            <flux:button variant="primary"
                         wire:click="addActionItemFromModal"
            >
                {{ __('minutes.create.modal.add_action_item.btn') }}
            </flux:button>
        </section>
    </flux:modal>
</div>

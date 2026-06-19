<div>
    <flux:heading size="lg">{{ __('mails.history_heading') }}</flux:heading>
    <flux:text class="mb-4">{{ __('mails.history_description') }}</flux:text>

    @if($this->mailings->isEmpty())
        <flux:text>{{ __('mails.history_empty') }}</flux:text>
    @else

        {{-- ── Detail panel ─────────────────────────────────────────────── --}}
        @if($this->selectedMailing)
            @php $m = $this->selectedMailing; @endphp
            <flux:card class="mb-4 border border-zinc-200 dark:border-zinc-700">

                <div class="flex items-start justify-between gap-4">
                    <div>
                        <flux:heading size="md">{{ $m->subjectFor($detailLocale) }}</flux:heading>
                        <flux:text class="text-xs text-zinc-400 mt-0.5">
                            {{ __('mails.history_sender') }} <strong>{{ $m->sender?->name ?? '–' }}</strong>
                            {{ __('mails.on_date') }} {{ $m->created_at->locale('de')->isoFormat('DD. MMMM YYYY, HH:mm') }} {{ __('mails.oclock') }}
                        </flux:text>
                    </div>
                    <flux:button size="sm"
                                 variant="ghost"
                                 icon="x-mark"
                                 wire:click="selectMailing({{ $m->id }})"
                    />
                </div>

                {{-- Locale tabs --}}
                <flux:tab.group class="mt-4">
                    <flux:tabs>
                        <flux:tab name="de"
                                  wire:click="setDetailLocale('de')"
                        >DE</flux:tab>
                        <flux:tab name="hu"
                                  wire:click="setDetailLocale('hu')"
                        >HU</flux:tab>
                    </flux:tabs>

                    <flux:tab.panel name="{{ $detailLocale }}">
                        <div class="mt-3 space-y-3">
                            <div>
                                <flux:label>{{ __('mails.members.subject') }}</flux:label>
                                <p class="text-sm font-medium">{{ $m->subject[$detailLocale] ?? '–' }}</p>
                            </div>
                            <div>
                                <flux:label>{{ __('mails.members.message') }}</flux:label>
                                <p class="text-sm whitespace-pre-wrap">{{ $m->message[$detailLocale] ?? '–' }}</p>
                            </div>
                        </div>
                    </flux:tab.panel>
                </flux:tab.group>

                {{-- Meta row --}}
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4 text-sm">
                    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-3 text-center">
                        <p class="text-2xl font-bold">{{ $m->recipient_count }}</p>
                        <p class="text-xs text-zinc-500">{{ __('mails.history_recipients_total') }}</p>
                    </div>
                    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-3 text-center">
                        <p class="text-2xl font-bold">{{ $m->member_count }}</p>
                        <p class="text-xs text-zinc-500">{{ __('mails.history_members') }}</p>
                    </div>
                    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-3 text-center">
                        <p class="text-2xl font-bold">{{ $m->mailing_list_count }}</p>
                        <p class="text-xs text-zinc-500">{{ __('mails.history_mailing_list') }}</p>
                    </div>
                    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-3 text-center">
                        <p class="text-2xl font-bold">
                            @if($m->attachments)
                                {{ count($m->attachments) }}
                            @else
                                0
                            @endif
                        </p>
                        <p class="text-xs text-zinc-500">{{ __('mails.history_attachments') }}</p>
                    </div>
                </div>

                {{-- Options badges --}}
                <div class="mt-3 flex flex-wrap gap-2">
                    @if($m->include_mailing_list)
                        <flux:badge color="blue" size="sm">{{ __('mails.history_included_mailing_list') }}</flux:badge>
                    @endif
                    @if($m->set_link)
                        <flux:badge color="lime" size="sm">{{ __('mails.tool.create_link') }}</flux:badge>
                    @endif
                    @if($m->set_personal_greeting)
                        <flux:badge color="lime" size="sm">{{ __('mails.history_personal_greeting_enabled') }}</flux:badge>
                    @endif
                    @if($m->set_attachment)
                        <flux:badge color="lime" size="sm">{{ __('mails.history_attachments_enabled') }}</flux:badge>
                    @endif
                    @if($m->url)
                        <flux:badge color="zinc" size="sm">{{ $m->url }}</flux:badge>
                    @endif
                </div>

                {{-- Attachments list --}}
                @if($m->attachments && count($m->attachments))
                    <div class="mt-3">
                        <flux:label>{{ __('mails.history_attachments_label') }}</flux:label>
                        <ul class="mt-1 space-y-1">
                            @foreach($m->attachments as $att)
                                <li class="flex items-center gap-2 text-sm">
                                    <flux:icon.paper-clip class="size-3.5 text-zinc-400"/>
                                    <span class="font-mono">{{ $att['original'] }}</span>
                                    <flux:badge size="sm">{{ strtoupper($att['locale']) }}</flux:badge>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </flux:card>
        @endif

        {{-- ── List ─────────────────────────────────────────────────────── --}}
        <flux:table :paginate="$this->mailings">
            <flux:table.columns>
                <flux:table.column>{{ __('common.date') }}</flux:table.column>
                <flux:table.column>{{ __('mails.members.subject') }} (DE)</flux:table.column>
                <flux:table.column>{{ __('mails.history_sender') }}</flux:table.column>
                <flux:table.column>
                    <flux:icon.users class="size-4"/>
                </flux:table.column>
                <flux:table.column/>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($this->mailings as $mailing)
                    <flux:table.row
                            :key="$mailing->id"
                            class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors
                               {{ $selected === $mailing->id ? 'bg-zinc-50 dark:bg-zinc-800' : '' }}"
                            wire:click="selectMailing({{ $mailing->id }})"
                    >
                        <flux:table.cell class="text-xs text-zinc-500 whitespace-nowrap">
                            {{ $mailing->created_at->locale('de')->isoFormat('DD.MM.YY HH:mm') }}
                        </flux:table.cell>

                        <flux:table.cell class="max-w-xs truncate font-medium">
                            {{ $mailing->subjectFor('de') }}
                        </flux:table.cell>

                        <flux:table.cell class="text-sm text-zinc-600">
                            {{ $mailing->sender?->name ?? '–' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge color="zinc"
                                        size="sm"
                            >{{ $mailing->recipient_count }}</flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:icon.chevron-right
                                    class="size-4 text-zinc-400 transition-transform
                                       {{ $selected === $mailing->id ? 'rotate-90' : '' }}"
                            />
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

    @endif
</div>
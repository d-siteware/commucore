<div class="space-y-6">
    <flux:card class="space-y-6">
        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ __('sepa.mandate.heading') }}</flux:heading>

            @can('update', $member)
                <flux:button wire:click="openCreateForm"
                             variant="primary"
                             size="sm"
                             icon="plus"
                >{{ __('sepa.mandate.actions.create') }}</flux:button>
            @endcan
        </div>

        @if($activeMandate)
            <flux:callout icon="check-circle" color="emerald">
                <flux:callout.heading>{{ __('sepa.mandate.messages.active') }}</flux:callout.heading>
                <flux:callout.text>
                    {{ __('sepa.mandate.fields.mandate_reference') }}: {{ $activeMandate->mandate_reference }}
                    &middot;
                    {{ __('sepa.mandate.fields.iban') }}: {{ \App\Models\Membership\SepaMandate::generateIbanHash($activeMandate->iban) }}
                </flux:callout.text>
            </flux:callout>
        @endif

        @if($mandates->isEmpty() && ! $showForm)
            <flux:subheading>{{ __('sepa.mandate.messages.no_mandate') }}</flux:subheading>
        @endif
    </flux:card>

    @if($mandates->isNotEmpty())
        <flux:card class="space-y-6">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('sepa.mandate.fields.mandate_reference') }}</flux:table.column>
                    <flux:table.column>{{ __('sepa.mandate.fields.iban') }}</flux:table.column>
                    <flux:table.column>{{ __('sepa.mandate.fields.account_holder') }}</flux:table.column>
                    <flux:table.column>{{ __('sepa.mandate.fields.mandate_type') }}</flux:table.column>
                    <flux:table.column>{{ __('sepa.mandate.fields.status') }}</flux:table.column>
                    <flux:table.column>{{ __('sepa.mandate.fields.mandate_date') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($mandates as $mandate)
                        <flux:table.row :key="$mandate->id">
                            <flux:table.cell variant="strong" class="font-mono text-xs">
                                {{ $mandate->mandate_reference }}
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-xs">
                                {{ \App\Models\Membership\SepaMandate::generateIbanHash($mandate->iban) }}
                            </flux:table.cell>
                            <flux:table.cell>{{ $mandate->account_holder }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="zinc">{{ $mandate->mandate_type->label() }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" color="{{ $mandate->status->color() }}">
                                    {{ $mandate->status->label() }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ \App\Helpers\DateHelper::formatDate($mandate->mandate_date) }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:dropdown align="end">
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal"></flux:button>

                                    <flux:menu>
                                        @if($mandate->isUsable())
                                            @can('update', $member)
                                                <flux:menu.item wire:click="edit({{ $mandate->id }})"
                                                                icon="pencil"
                                                >{{ __('common.edit') }}</flux:menu.item>
                                                <flux:menu.separator />
                                                <flux:menu.item wire:click="cancel({{ $mandate->id }})"
                                                                icon="x-circle"
                                                                variant="danger"
                                                >{{ __('sepa.mandate.actions.cancel') }}</flux:menu.item>
                                            @endcan
                                        @endif

                                        @if($mandate->signedDocument)
                                            <flux:menu.separator />
                                            <flux:menu.item icon="document-arrow-down"
                                                            wire:click="downloadDocument({{ $mandate->signedDocument->id }})"
                                            >{{ __('common.download') }}</flux:menu.item>
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    @endif

    <flux:modal wire:model="showForm"
                variant="flyout"
                position="right"
                class="space-y-6"
    >
        <flux:heading size="lg">
            {{ $editing ? __('common.edit') : __('sepa.mandate.actions.create') }}
        </flux:heading>

        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model.blur="account_holder"
                        label="{{ __('sepa.mandate.fields.account_holder') }}"
                        placeholder="{{ $member->fullName() }}"
                        required
            />

            <flux:input wire:model.blur="iban"
                        label="{{ __('sepa.mandate.fields.iban') }}"
                        placeholder="DE89 3704 0044 0532 0130 00"
                        maxlength="34"
                        required
            />

            <flux:input wire:model.blur="bic"
                        label="{{ __('sepa.mandate.fields.bic') }}"
                        placeholder="COBADEFFXXX"
                        maxlength="11"
            />

            <flux:select wire:model="mandate_type"
                         label="{{ __('sepa.mandate.fields.mandate_type') }}"
                         variant="listbox"
                         required
            >
                @foreach(\App\Enums\SepaMandateType::options() as $value => $label)
                    <flux:select.option :value="$value">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:file-upload wire:model="sepa_documents" multiple label="{{ __('sepa.mandate.fields.sepa_documents') }}" accept="application/pdf">
                <flux:file-upload.dropzone
                    heading="{{ __('sepa.mandate.fields.sepa_documents_dropzone_heading') }}"
                    text="{{ __('sepa.mandate.fields.sepa_documents_dropzone_text') }}"
                />
            </flux:file-upload>
            @error('sepa_documents.*')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror

            <flux:textarea wire:model.blur="notes"
                           rows="auto"
                           label="{{ __('sepa.mandate.fields.notes') }}"
            />

            <div class="flex gap-2">
                <flux:spacer />

                <flux:modal.close>
                    <flux:button variant="ghost" type="button">
                        {{ __('common.cancel') }}
                    </flux:button>
                </flux:modal.close>

                <flux:button variant="primary" type="submit">
                    {{ __('common.save') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>

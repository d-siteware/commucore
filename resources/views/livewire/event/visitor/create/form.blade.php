
<div class="pt-10 space-y-6">


    <flux:heading size="lg">{{ __('event.visitor-modal.heading') }}</flux:heading>


    <form wire:submit="add"
          class="space-y-6"
    >
        <flux:separator text="{{ __('event.visitor-modal.separator.values') }}"/>

        <flux:input wire:model.blur="form.name"
                    label="{{ __('event.visitor-modal.name') }}"
                    autocomplete="name"
        />
        <flux:input wire:model.blur="form.email"
                    type="email"
                    label="{{ __('event.visitor-modal.email') }}"
                    autocomplete="email"
        />
        <flux:input wire:model.blur="form.phone"
                    type="tel"
                    mask="+99 999999999999"
                    label="{{ __('event.visitor-modal.phone') }}"
        />

        <flux:field>
                    <flux:radio.group wire:model.live="form.gender"
                              variant="segmented"
            >
                @foreach(\App\Enums\Gender::options() as $value => $label)
                    <flux:radio value="{{ $value }}"
                                label="{{ $label }}"
                    />
                @endforeach
            </flux:radio.group>
            <flux:error name="form.gender"/>
        </flux:field>

        <flux:separator text="{{ __('event.visitor-modal.separator.optional') }}"/>

        <flux:select wire:model="form.member_id"
                     variant="listbox"
                     searchable
                     clearable
                     placeholder="{{ __('event.visitor-modal.select_member') }}"
                     wire:change="setMember"
        >
            @foreach($members as $member)
                <flux:select.option value="{{ $member->id }}"
                                    wire:key="{{ $member->id }}"
                >{{ $member->fullName() }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:separator text="{{ __('event.visitor-modal.separator.or') }}"/>

        <flux:select wire:model="form.event_subscription_id"
                     variant="listbox"
                     searchable
                     clearable
                     placeholder="{{ __('event.visitor-modal.select_subscribers') }}"
                     wire:change="setSubscriber"
        >
            @foreach($subscribers as $subscriber)
                <flux:select.option value="{{ $subscriber->id }}"
                                    wire:key="{{ $subscriber->id }}"
                >{{ $subscriber->name }}</flux:select.option>
            @endforeach
        </flux:select>


        <flux:button variant="primary"
                     type="submit"
        >{{ __('event.visitor-modal.btn.submit') }}</flux:button>
    </form>
{{--    @dump($form)--}}
</div>

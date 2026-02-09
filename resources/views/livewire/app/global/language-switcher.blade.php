<div>

    <flux:radio.group variant="segmented"
                      size="sm"
    >
        @foreach(\App\Models\Locale::getNames() as $locale)
            <flux:radio
                    wire:click="switchLanguage('{{ $locale }}')"
                    :checked="$currentLocale === $locale"
                    :label="$locale"
            />
        @endforeach

    </flux:radio.group>

</div>

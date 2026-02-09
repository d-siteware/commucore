@php use App\Enums\Gender; @endphp
<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        {{ __('profile.page.title') }}
    </x-slot>

    <x-slot name="description">
        {{ __('profile.section.profile.description') }}
    </x-slot>

    <x-slot name="form">
        <!-- Profile Photo -->
        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
            <div x-data="{photoName: null, photoPreview: null}"
                 class="col-span-6 sm:col-span-4"
            >
                <!-- Profile Photo File Input -->
                <input type="file"
                       id="photo"
                       class="hidden"
                       wire:model.live="photo"
                       x-ref="photo"
                       x-on:change="
                                    photoName = $refs.photo.files[0].name;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        photoPreview = e.target.result;
                                    };
                                    reader.readAsDataURL($refs.photo.files[0]);
                            "
                />


                <!-- Current Profile Photo -->
                <div class="mt-2"
                     x-show="! photoPreview"
                >
                    <img src="{{ $this->user->profile_photo_url }}"
                         alt="{{ $this->user->name }}"
                         class="rounded-full size-20 object-cover"
                    >
                </div>

                <!-- New Profile Photo Preview -->
                <div class="mt-2"
                     x-show="photoPreview"
                     style="display: none;"
                >
                    <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                          x-bind:style="'background-image: url(\'' + photoPreview + '\');'"
                    >
                    </span>
                </div>

                <flux:button variant="filled"
                             size="xs"
                             class="mt-2 me-2"
                             type="button"
                             x-on:click.prevent="$refs.photo.click()"
                >
                    {{ __('Select A New Photo') }}
                </flux:button>

                @if ($this->user->profile_photo_path)
                    <x-secondary-button type="button"
                                        class="mt-2"
                                        wire:click="deleteProfilePhoto"
                    >
                        {{ __('Remove Photo') }}
                    </x-secondary-button>
                @endif
                <flux:error name="photo"/>

            </div>
        @endif



        <!-- Name -->
        <div class="col-span-6 sm:col-span-4">
            <flux:input wire:model="state.name"
                        label="{{ __('profile.label.name') }}"
            />
        </div>

        <!-- First Name -->
        <div class="col-span-6 sm:col-span-4">
            <flux:input wire:model="state.name"
                        label="{{ __('profile.label.first_name') }}"
            />
        </div>

        <!-- Phone -->
        <div class="col-span-6 sm:col-span-4">
            <flux:input wire:model="state.phone"
                        label="{{ __('profile.label.phone') }}"
                        mask="(+99) 99 99999999"
                        placeholder="(+49) 30 40586940 "
            />
        </div>

        <!-- Mobile -->
        <div class="col-span-6 sm:col-span-4">
            <flux:input wire:model="state.mobile"
                        label="{{ __('profile.label.mobile') }}"
                        mask="(+99) 999 99999999"
                        placeholder="(+49) 173 55079408 "
            />
        </div>

        <!-- Locale and Gender -->
        <div class="col-span-6 sm:col-span-4 space-y-6">

            <flux:radio.group wire:model="state.gender"
                              label="{{ __('profile.label.gender') }}"
                              size="sm"
                              variant="segmented"
            >
                @foreach(Gender::toArray() as $gender)
                    <flux:radio label="{{ Gender::value($gender) }}"
                                :value="$gender"
                    />
                @endforeach
            </flux:radio.group>

            <flux:radio.group wire:model="state.locale"
                              label="{{ __('profile.label.locale') }}"
                              variant="segmented"
                              size="sm"
            >
                @foreach(\App\Models\Locale::getNames() as $locale)
                    <flux:radio label="{{ $locale }}"
                                :value="$locale"
                    />
                @endforeach
            </flux:radio.group>

        </div>

        <!-- Email -->
        <div class="col-span-6 sm:col-span-4">
            <flux:input wire:model="state.email"
                        label="{{ __('profile.label.email') }}"
                        required
            />

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                <p class="text-sm mt-2 dark:text-white">
                    {{ __('profile.email.verification.status.unverified') }}

                    <button type="button"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-offset-gray-800"
                            wire:click.prevent="sendEmailVerification"
                    >
                        {{ __('profile.email.verification.resend.label') }}
                    </button>
                </p>

                @if ($this->verificationLinkSent)
                    <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ __('profile.email.verification.resend.status') }}
                    </p>
                @endif
            @endif
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3"
                          on="saved"
        >
            {{ __('Saved.') }}
        </x-action-message>

        <flux:button type="submit"
                     wire:loading.attr="disabled"
                     wire:target="photo"
        >
            {{ __('Save') }}
        </flux:button>
    </x-slot>
</x-form-section>

@if($leader)
    @php
        $profile_link = $leader->profile_image
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($leader->profile_image)
            : null;
    @endphp
    <li class="flex gap-4 lg:gap-6 py-6 first:pt-0 last:pb-0" wire:key="{{ $leader->id }}">

        @if($profile_link)
            <img class="aspect-square size-14 lg:size-24 xl:size-32 flex-none rounded-full lg:rounded-2xl object-cover"
                 src="{{ $profile_link }}"
                 alt="{{ __('role.leadership.profile_image_alt', ['name' => $leader->member->fullName()]) }}">
        @else
            <x-empty_profile_img :name="$leader->member->first_name.' '.$leader->member->name"
                                 class="aspect-square size-14 lg:size-24 xl:size-32 flex-none rounded-full lg:rounded-2xl text-lg lg:text-2xl"/>
        @endif

        <div class="min-w-0 flex-auto">
            <h3 class="text-base/7 lg:text-lg/8 font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $leader->member->fullName() }}</h3>
            <p class="text-sm/6 lg:text-base/7 text-zinc-600 dark:text-zinc-300">{{ $leader->role->name[app()->getLocale()] }}</p>

            @if($about = $leader->about_me[app()->getLocale()] ?? null)
                <p class="mt-2 lg:mt-3 text-sm/6 text-zinc-600 dark:text-zinc-400">{{ $about }}</p>
            @endif

            <a href="mailto:{{ $leader->member->email }}"
               class="mt-2 lg:mt-3 inline-block text-sm/6 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 underline"
            >{{ $leader->member->email }}</a>
        </div>

        <div class="flex flex-none flex-col gap-2">
            @can('update', $leader)
                <flux:button size="sm"
                             wire:click="editMemberRole({{ $leader->id }})"
                             aria-label="{{ __('role.leadership.edit_label') }}"
                >
                    <flux:icon.pencil-square class="size-4"/>
                </flux:button>
            @endcan

            @can('delete', $leader)
                <flux:button size="sm"
                             wire:click="removeMemberRole({{ $leader->id }})"
                             wire:confirm="{{ __('role.leadership.confirm_remove') }}"
                             aria-label="{{ __('role.leadership.remove_label') }}"
                >
                    <flux:icon.trash class="size-4 text-red-600"/>
                </flux:button>
            @endcan
        </div>

    </li>
@endif

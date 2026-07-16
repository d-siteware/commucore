@if($leader)
    @php
    $profile_link = $leader->profile_image ?  \Illuminate\Support\Facades\Storage::disk('public')->url($leader->profile_image) : 'https://ui-avatars.com/api/?name='.urlencode($leader->member->first_name.' '.$leader->member->name).'&color=7F9CF5&background=EBF4FF';

    @endphp
    <li class="flex  gap-10 py-12 first:pt-0 last:pb-0 " wire:key="{{ $leader->id }}">

        <img class="aspect-square w-30 xl:aspect-4/5 xl:w-60 flex-none rounded-2xl xl:object-cover " src="{{ $profile_link }}" alt="Profile image {{ $leader->member->fullName() }}">
        <div class="max-w-xl flex-auto">
            <h3 class="text-lg/8 font-semibold tracking-tight text-gray-900 dark:text-zinc-300">{{ $leader->member->fullName() }}</h3>
            <p class="text-base/7 text-gray-600 dark:text-zinc-300">{{ $leader->role->name[app()->getLocale()] }}</p>

            <p class="mt-3 lg:mt-6 text-base/7 text-gray-600 dark:text-zinc-400">{{ $leader->about_me[app()->getLocale()]??'-' }}</p>

            <ul role="list" class="mt-3 lg:mt-6 flex gap-x-6">
                <li>
                    <a href="mailto:{{ $leader->member->email }}" class="text-gray-400 hover:text-gray-500 underline">
                        {{ $leader->member->email }}
                    </a>
                </li>
            </ul>


        </div>

       <div class="flex flex-col gap-2">
           @can('update', $leader)
           <flux:button size="xs" wire:click="editMemberRole({{ $leader->id }})">
               <flux:icon.pencil-square class="size-4" />
           </flux:button>
           @endcan

           @can('delete', $leader)
               <flux:button size="xs" wire:click="removeMemberRole({{ $leader->id }})">
                   <flux:icon.trash class="size-4 text-red-600" />
               </flux:button>
           @endcan
       </div>

    </li>
@endif

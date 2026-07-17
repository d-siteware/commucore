<div class="flex items-center gap-3 p-3 border border-zinc-200 dark:border-zinc-700 rounded-xl bg-white dark:bg-zinc-800/50" {{ $attributes }}>
    @can('update', $role)
        <flux:icon.arrows-up-down x-sort:handle
                                  class="size-5 shrink-0 cursor-grab text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 active:cursor-grabbing"
        />
    @endcan

    <div class="min-w-0 flex-auto">
        <flux:heading size="sm">{{ $role->name[app()->getLocale()] }}</flux:heading>
        @if($role->description)
            <flux:text class="text-sm">{{ $role->description }}</flux:text>
        @endif
    </div>

    @can('update', $role)
        <div class="flex flex-none gap-1">
            <flux:button size="sm"
                         wire:click="editRole({{ $role->id }})"
                         aria-label="{{ __('role.leadership.edit_role_label') }}"
            >
                <flux:icon.pencil-square class="size-4"/>
            </flux:button>
            @can('delete', $role)
                <flux:button size="sm"
                             wire:click="deleteRole({{ $role->id }})"
                             wire:confirm="{{ __('role.delete.confirm') }}"
                             aria-label="{{ __('role.leadership.delete_role_label') }}"
                >
                    <flux:icon.trash class="size-4"/>
                </flux:button>
            @endcan
        </div>
    @endcan
</div>

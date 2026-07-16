<div class="p-3 border border-zinc-300 my-3 rounded-xl flex justify-between items-center " {{ $attributes }}>
    @can('update', $role)
        <flux:icon.arrows-up-down x-sort:handle
                                  class="size-4 hover:cursor-move"
        />
    @endcan
    <div>
        <flux:heading>{{ $role->name[app()->getLocale()] }}</flux:heading>
        <flux:text>{{ $role->description }}</flux:text>
    </div>
    @can('update', $role)
        <aside class="flex flex-col gap-2">
            <flux:button size="xs"
                         wire:click="editRole({{ $role->id }})"
            >
                <flux:icon.pencil-square class="size-4"/>
            </flux:button>
            @can('delete', $role)
                <flux:button size="xs"
                             wire:click="deleteRole({{ $role->id }})"
                >
                    <flux:icon.trash class="size-4"/>
                </flux:button>
            @endcan
        </aside>
    @endcan
</div>

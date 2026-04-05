<div class="p-3 border border-zinc-300 my-3 rounded-xl flex justify-between items-center " {{ $attributes }}>
    @if($edit)
        <flux:icon.arrows-up-down x-sort:handle
                                  class="size-4 hover:cursor-move"
        />
    @endif
    <div>
        <flux:heading>{{ $role->name[app()->getLocale()] }}</flux:heading>
        <flux:text>{{ $role->description }}</flux:text>
    </div>
    @if($edit)
        <aside class="flex flex-col gap-2">
            <flux:button size="xs"
                         wire:click="editRole({{ $role->id }})"
            >
                <flux:icon.pencil-square class="size-4"/>
            </flux:button>
            <flux:button size="xs"
                         wire:click="deleteRole({{ $role->id }})"
            >
                <flux:icon.trash class="size-4"/>
            </flux:button>
        </aside>
    @endif
</div>

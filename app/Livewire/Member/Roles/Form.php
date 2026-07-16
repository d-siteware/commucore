<?php

declare(strict_types=1);

namespace App\Livewire\Member\Roles;

use App\Livewire\Forms\Member\MemberRoleForm;
use App\Livewire\Forms\Member\RoleForm;
use App\Livewire\Traits\HandlesErrors;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Membership\Member;
use App\Models\Membership\MemberRole;
use App\Models\Membership\Role;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

final class Form extends Component
{
    use HandlesErrors;
    use HasPrivileges;
    use WithFileUploads;

    public $form;

    public RoleForm $roleForm;

    public MemberRoleForm $memberRoleForm;

    protected bool $edit = false;

    #[Computed]
    public function roles()
    {
        return Role::query()
            ->select('id', 'name')
            ->get()
            ->filter(function ($role): bool {
                return ! MemberRole::query()
                    ->where('role_id', $role->id)
                    ->exists();
            });
    }

    #[Computed]
    public function members(): Collection
    {
        return Member::query()
            ->select('id', 'first_name', 'name')
            ->whereDoesntHave('roles')
            ->get();
    }

    public function mount(?Role $role, ?MemberRole $memberRole): void
    {
        $this->roleForm = new RoleForm($this, $role);
        $this->memberRoleForm = new MemberRoleForm($this, $memberRole);

        if ($memberRole->id) {
            $this->memberRoleForm->set($memberRole->id);
            $this->edit = true;
        }
    }

    public function save(): void
    {
        try {
            $this->checkPrivilege(Role::class);

            if ($this->edit) {
                $this->memberRoleForm->update();
                $msg = __('role.toast.msg.leaderrole.updated');
            } else {
                $this->memberRoleForm->create();
                $msg = __('role.toast.msg.leaderrole.assigened');
            }

            Flux::toast($msg, 'success');

            $this->dispatch('memberRolesUpdated');
        } catch (\Throwable $e) {
            $this->handleError('Rollen-Formular speichern fehlgeschlagen', $e);
        }
    }

    public function addRole(): void
    {
        try {
            $this->checkPrivilege(Role::class);
            $role = $this->roleForm->create();
            Flux::modal('make-new-role')
                ->close();
            $this->roleForm->id = $role->id;
        } catch (\Throwable $e) {
            $this->handleError('Rolle hinzufügen fehlgeschlagen', $e);
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.assign-role.form');
    }
}

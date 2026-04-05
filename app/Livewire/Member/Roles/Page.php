<?php

declare(strict_types=1);

namespace App\Livewire\Member\Roles;

use App\Livewire\Forms\Member\MemberRoleForm;
use App\Livewire\Forms\Member\RoleForm;
use App\Livewire\Traits\HasPrivileges;
use App\Models\Membership\Member;
use App\Models\Membership\MemberRole;
use App\Models\Membership\Role;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

final class Page extends Component
{
    use HasPrivileges;
    use WithFileUploads;
    use WithPagination;

    public Member $member;

    public MemberRoleForm $memberRoleForm;

    public RoleForm $roleForm;

    public bool $edit = false;

    #[Computed]
    public function leadershipRooster(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return MemberRole::query()->with('member')->with('role')->paginate();
    }

    public string $locale;

    #[Computed]
    public function roles(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return Role::query()->select('id', 'name', 'sort')->orderBy('sort')->paginate(10);
    }

    #[Computed]
    public function members(): Collection
    {
        return Member::query()->select('id', 'name', 'first_name')->get();
    }

    #[Computed]
    public function avaliableRoles(): Collection|\Illuminate\Support\Collection
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
    public function avaliableMembers()
    {
        return Member::query()
            ->select('id', 'first_name', 'name')
            ->get()
            ->filter(function ($member): bool {
                return ! $member->roles()
                    ->exists();
            });
    }

    public function sortItem($item, $position): void
    {

        $role = Role::query()->findOrFail($item);

        $this->moveItem($role, $position);

    }

    /**
     * @throws \Throwable
     */
    protected function moveItem($role, $position): void
    {

        DB::transaction(function () use ($role, $position): void {

            $current = $role->sort;

            if ($current === $position) {
                return;
            }

            $role->update([
                'sort' => 999999,
            ]);

            $block = Role::query()->whereBetween('sort', [
                min($current, $position),
                max($current, $position),
            ]);

            $shiftBlockDown = $current < $position;

            $shiftBlockDown
                ? $block->decrement('sort')
                : $block->increment('sort');

            $role->update([
                'sort' => $position,
            ]);

        });

    }

    public function mount(): void
    {

        $this->locale = app()->getLocale();
        $this->memberRoleForm = new MemberRoleForm($this, 'memberRoleForm');
        $this->roleForm = new RoleForm($this, 'roleForm');
        $this->memberRoleForm->init();

        $user = Auth::user();

        $this->edit = $user->is_admin || $user->isBoardMember();
    }

    public function deleteRole(int $roleId): void
    {

        $this->checkPrivilege(Role::class);

        $role = Role::query()->findOrFail($roleId);

        $this->moveItem($role, 9999999999);

        $role->delete();

    }

    public function removeMemberRole(int $memberRoleId): void
    {
        $this->checkPrivilege(MemberRole::class);
        MemberRole::query()->findOrFail($memberRoleId)->delete();
        Flux::toast(__('role.toast.msg.leaderrole.revoked'), 'success');
    }

    public function editMemberRole(int $memberRoleId): void
    {

        $this->checkPrivilege(MemberRole::class);

        $this->memberRoleForm->set($memberRoleId);
        $this->edit = true;

        Flux::modal('add-member-to-leaderboard')->show();

    }

    public function editRole(int $roleId): void
    {

        $this->checkPrivilege(Role::class);

        $this->roleForm->set($roleId);

        Flux::modal('make-new-role')->show();

    }

    public function saveMemberRole(): void
    {
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

    }

    public function addRole(): void
    {

        $this->checkPrivilege(Role::class);

        $role = $this->roleForm->create();
        Flux::modal('make-new-role')
            ->close();
        $this->roleForm->id = $role->id;
    }

    public function deleteProfileImage(): void
    {
        $this->checkPrivilege(MemberRole::class);

        $this->memberRoleForm->profile_image = null;

    }

    public function render()
    {
        return view('livewire.member.roles.page')->title(__('role.page.title', ['name' => setting('organization.name')]));
    }
}

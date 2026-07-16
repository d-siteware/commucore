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
use App\Services\OnboardingStatusService;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

final class Page extends Component
{
    use HandlesErrors;
    use HasPrivileges;
    use WithFileUploads;
    use WithPagination;

    public Member $member;

    public MemberRoleForm $memberRoleForm;

    public RoleForm $roleForm;

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
        return Member::query()
            ->select('id', 'name', 'first_name')
            ->whereDoesntHave('roles')
            ->get();
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
    public function avaliableMembers(): Collection
    {
        return Member::query()
            ->select('id', 'first_name', 'name')
            ->whereDoesntHave('roles')
            ->get();
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
    }

    private function invalidateOnboarding(): void
    {
        app(OnboardingStatusService::class)->invalidate();
    }

    public function deleteRole(int $roleId): void
    {
        try {
            $this->checkPrivilege(Role::class);

            $role = Role::query()->findOrFail($roleId);

            $this->moveItem($role, 9999999999);

            $role->delete();

            $this->invalidateOnboarding();
            $this->dispatch('onboarding-update');
        } catch (\Throwable $e) {
            $this->handleError('Rolle löschen fehlgeschlagen', $e);
        }
    }

    public function attachMemberRole(): void
    {
        $this->checkPrivilege(MemberRole::class);
        Flux::modal('add-member-to-leaderboard')->show();
    }

    public function removeMemberRole(int $memberRoleId): void
    {
        $this->checkPrivilege(MemberRole::class);
        MemberRole::query()->findOrFail($memberRoleId)->delete();

        $this->invalidateOnboarding();

        $this->dispatch('onboarding-update');
        Flux::modal('add-member-to-leaderboard')->close();
        Flux::toast(text: __('role.toast.msg.leaderrole.revoked'), variant: 'success');
    }

    public function editMemberRole(int $memberRoleId): void
    {

        $this->checkPrivilege(MemberRole::class);
        $this->memberRoleForm->set($memberRoleId);

        Flux::modal('add-member-to-leaderboard')->show();

    }


    public function saveMemberRole(): void
    {
        try {
            $this->checkPrivilege(MemberRole::class);

            $isUpdate = $this->memberRoleForm->id !== null;

            if ($isUpdate) {
                $this->memberRoleForm->update();
                $msg = __('role.toast.msg.leaderrole.updated');
            } else {
                $this->memberRoleForm->create();
                $msg = __('role.toast.msg.leaderrole.assigened');
            }

            Flux::toast(text:$msg, variant:'success');

            $this->dispatch('memberRolesUpdated');
            $this->dispatch('onboarding-update');
            Flux::modal('add-member-to-leaderboard')->close();
        } catch (\Throwable $e) {
            $this->handleError('Rollen-Zuweisung speichern fehlgeschlagen', $e);
        }
    }

    public function addRole(): void
    {
        $this->checkPrivilege(Role::class);
        Flux::modal('make-new-role')->show();
    }

    public function storeRole(): void
    {
        try {
            $this->checkPrivilege(Role::class);

            if ($this->roleForm->id !== null) {
                $this->updateRole();
            } else {
                $this->storeNewRole();
            }
        } catch (\Throwable $e) {
            $this->handleError('Rolle speichern fehlgeschlagen', $e);
        }
    }

    private function storeNewRole(): void
    {
        try {
            $this->checkPrivilege(Role::class);

            $role = $this->roleForm->create();

            $this->invalidateOnboarding();
            $this->dispatch('onboarding-update');

            Flux::modal('make-new-role')
                ->close();
            $this->roleForm->id = $role->id;
            Flux::toast(text: __('common.success'), variant:'success');
        } catch (\Throwable $e) {
            $this->handleError('Neue Rolle erstellen fehlgeschlagen', $e);
        }
    }

    public function editRole(int $roleId): void
    {

        $this->checkPrivilege(Role::class);
        $this->roleForm->set($roleId);

        Flux::modal('make-new-role')->show();

    }
    private function updateRole(): void
    {
        $this->checkPrivilege(Role::class);

        $role = Role::query()->findOrFail($this->roleForm->id);
        try {
            $this->roleForm->update($role);
            $this->invalidateOnboarding();
            $this->dispatch('onboarding-update');
            Flux::modal('make-new-role')->close();
            Flux::toast(text: __('common.success'), variant:'success');
        } catch (\Throwable $e) {
            $this->handleError('Rolle aktualisieren fehlgeschlagen', $e);
        }

    }

    public function deleteProfileImage(): void
    {
        try {
            $this->checkPrivilege(MemberRole::class);

            $this->memberRoleForm->profile_image = null;
        } catch (\Throwable $e) {
            $this->handleError('Profilbild löschen fehlgeschlagen', $e);
        }
    }

    public function render()
    {
        return view('livewire.member.roles.page')->title(__('role.page.title', ['name' => setting('organization.name')]));
    }
}

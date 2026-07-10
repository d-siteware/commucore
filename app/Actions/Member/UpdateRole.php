<?php

declare(strict_types=1);

namespace App\Actions\Member;

use App\Livewire\Forms\Member\RoleForm;
use App\Models\Membership\Role;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;

final class UpdateRole extends Action
{
    public static function handle(RoleForm $form, Role $role): Role
    {
        return DB::transaction(function () use ($form, $role) {
            $role->update([
                'name' => $form->name,
                'description' => $form->description,
                'sort' => $form->sort,
                'can_manage_accounting' => $form->can_manage_accounting,
                'can_audit_accounting' => $form->can_audit_accounting,
                'can_represent_organization' => $form->can_represent_organization,
            ]);

            return $role->refresh();
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Member;

use App\Livewire\Forms\Member\RoleForm;
use App\Models\Membership\Role;
use Illuminate\Notifications\Action;
use Illuminate\Support\Facades\DB;

final class CreateRole extends Action
{
    public static function handle(RoleForm $form): Role
    {
        return DB::transaction(function () use ($form) {

            return Role::create([
                'name' => $form->name,
                'description' => $form->description,
                'sort' => $form->sort,
                'can_manage_accounting' => $form->can_manage_accounting,
            ]);

        });

    }
}

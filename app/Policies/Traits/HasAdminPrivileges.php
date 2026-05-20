<?php

declare(strict_types=1);

namespace App\Policies\Traits;

use App\Models\User;

/**
 * User
 */
trait HasAdminPrivileges
{
    protected function getAdminPrivileges(User $user): bool
    {

        if (! $user->exists) {
            return false;
        }

        if ($user->is_admin) {
            return true;
        }

        if ($user->isAccountant()) {
            return true;
        }

        if ($user->isBoardMember()) {
            return true;
        }

        return false;
    }
}

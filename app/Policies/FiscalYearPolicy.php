<?php

namespace App\Policies;

use App\Models\User;

class FiscalYearPolicy
{
    use \App\Policies\Traits\HasAdminPrivileges;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        // Nur Admins und Accountants dürfen Geschäftsjahre schließen
        return $this->getAdminPrivileges($user);
    }

    public function close(User $user): bool
    {
        // Nur Admins und Accountants dürfen Geschäftsjahre schließen
        return $this->getAdminPrivileges($user);
    }

    public function reopen(User $user): bool
    {
        return $user->is_admin;

        // Nur Admins dürfen geschlossene Geschäftsjahre wiedereröffnen
        //        return $this->getAdminPrivileges($user);
    }

    public function delete(User $user): bool
    {
        return $user->is_admin;
    }
}

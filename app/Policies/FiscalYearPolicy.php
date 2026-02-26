<?php

namespace App\Policies;

use App\Models\User;

class FiscalYearPolicy
{
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
        return $user->is_admin || $user->isAccountant();
    }

    public function close(User $user): bool
    {
        // Nur Admins und Accountants dürfen Geschäftsjahre schließen
        return $user->is_admin || $user->isAccountant();
    }

    public function reopen(User $user): bool
    {
        // Nur Admins dürfen geschlossene Geschäftsjahre wiedereröffnen
        return $user->is_admin;
    }
}

<?php

namespace App\Policies;

use App\Models\Accounting\FiscalYear;
use App\Models\User;

class FiscalYearPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    public function close(User $user, FiscalYear $fiscalYear): bool
    {
        // Nur Admins und Accountants dürfen Geschäftsjahre schließen
        return $user->is_admin || $user->isAccountant();
    }

    public function reopen(User $user, FiscalYear $fiscalYear): bool
    {
        // Nur Admins dürfen geschlossene Geschäftsjahre wiedereröffnen
        return $user->is_admin;
    }
}

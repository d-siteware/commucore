<?php

namespace App\Policies;

use App\Models\Funding\Funding;
use App\Models\User;

class FundingPolicy
{
    use \App\Policies\Traits\HasAdminPrivileges;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Funding $funding): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->getAdminPrivileges($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $this->getAdminPrivileges($user);

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Funding $funding): bool
    {
        if ($this->getAdminPrivileges($user)) {
            return ! $funding->remainingAmount() > 0;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Funding $funding): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Funding $funding): bool
    {
        return false;
    }
}

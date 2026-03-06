<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SharedImage;
use App\Models\User;
use App\Policies\Traits\HasAdminPrivileges;

final class SharedImagePolicy
{
    use HasAdminPrivileges;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(): bool
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
    public function update(User $user, SharedImage $sharedImage): bool
    {
        if ($sharedImage->user_id == $user->id) {
            return true;
        }

        return $this->getAdminPrivileges($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SharedImage $sharedImage): bool
    {
        if ($sharedImage->user_id == $user->id) {
            return true;
        }

        return $this->getAdminPrivileges($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return $this->getAdminPrivileges($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return $this->getAdminPrivileges($user);
    }
}

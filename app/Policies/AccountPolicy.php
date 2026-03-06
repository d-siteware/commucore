<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Policies\Traits\HasAdminPrivileges;
use Illuminate\Support\Facades\Auth;

final class AccountPolicy
{
    use HasAdminPrivileges;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user): bool
    {
        return $this->getAdminPrivileges($user);
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
    public function delete(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(): bool
    {
        return false;
    }

    public function audit(): bool
    {
        return false;
    }

    public function bookItem(): bool
    {
        return $this->getAdminPrivileges(Auth::user());
    }
}

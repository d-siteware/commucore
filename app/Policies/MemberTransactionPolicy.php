<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\MemberType;
use App\Models\Membership\MemberTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class MemberTransactionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MemberTransaction $memberTransaction): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->checkThis();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MemberTransaction $memberTransaction): bool
    {
        return $this->checkThis();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MemberTransaction $memberTransaction): bool
    {
        return $this->checkThis();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MemberTransaction $memberTransaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MemberTransaction $memberTransaction): bool
    {
        return false;
    }

    private function checkThis(): bool
    {
        $user = Auth::user();

        if ($user->is_admin) {
            return true;
        }

        if ($user->member && $user->member->type === MemberType::MD) {
            return true;
        }

        return false;
    }
}

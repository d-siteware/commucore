<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\MemberType;
use App\Models\Membership\Member;
use App\Models\User;
use App\Policies\Traits\HasAdminPrivileges;
use Illuminate\Support\Facades\Auth;

final class MemberPolicy
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
    public function view(User $user, Member $member): bool
    {
        if ($user->member && $user->member->id == $member->id) {
            return true;
        }

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
    public function update(User $user, Member $member): bool
    {

        if ($user->member && $user->member->id == $member->id) {
            return true;
        }

        if ($user->member && $user->member->type === MemberType::MD) {
            return true;
        }

        return $this->getAdminPrivileges($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(): bool
    {
        $user = Auth::user();

        if ($user->is_admin) {
            return true;
        }

        return (bool) $user->isBoardMember();
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

    public function export(User $user): bool
    {
        if ($user->is_admin) {
            return true;
        }

        // Board-Mitglieder dürfen exportieren
        return Member::query()
            ->where('user_id', $user->id)
            ->where('type', MemberType::MD->value)
            ->whereNull('left_at')
            ->whereNull('pseudonymized_at')
            ->exists();
    }
}

<?php

namespace App\Policies;

use App\Models\MemberChangeRequest;
use App\Models\User;

class MemberChangeRequestPolicy
{
    public function viewAny(User $user): bool
    {

        if ($user->is_admin) {
            return true;
        }

        if ($user->isBoardMember()) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->member !== null;
    }

    public function review(User $user, MemberChangeRequest $request): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $member = $user->member;

        return $member !== null && $member->isBoardMember();
    }

    public function view(User $user, MemberChangeRequest $request): bool
    {
        if ($user->is_admin) {
            return true;
        }

        $member = $user->member;

        if ($member === null) {
            return false;
        }

        return $member->isBoardMember()
            || $member->id === $request->member_id;
    }
}

<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Membership\Member;
use App\Notifications\MemberAcceptedNotification;

final class MemberObserver
{
    public function updated(Member $member): void
    {
        if ($member->wasChanged('entered_at') && $member->entered_at !== null) {
            $member->notify(new MemberAcceptedNotification($member));
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Livewire\Member\Apply;

use App\Models\Membership\Member;
use App\Models\Membership\MemberApplication;
use App\Notifications\MemberApplicationVerifiedNotification;
use App\Notifications\NewMemberAppliedNotification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class Page extends Component
{
    public bool $isExternalMemberApplication = true;

    /** @var 'form'|'pending'|'verify'|'done'|'expired'|'invalid' */
    public string $step = 'form';

    public bool $gdpr_consent = false;

    public bool $newsletter_consent = false;

    public bool $photo_consent = false;

    public ?MemberApplication $application = null;

    public function mount(): void
    {
        $token = request()->query('token');

        if (! is_string($token) || $token === '') {
            return;
        }

        $this->application = MemberApplication::query()
            ->where('token', $token)
            ->first();

        if ($this->application === null) {
            $this->step = 'invalid';

            return;
        }

        if ($this->application->isExpired()) {
            $this->step = 'expired';

            return;
        }

        if ($this->application->isVerified()) {
            $this->step = 'done';

            return;
        }

        $this->step = 'verify';
    }

    public function applicationSubmitted(): void
    {
        $this->step = 'pending';
    }

    public function confirmConsents(): void
    {
        $this->validate([
            'gdpr_consent' => ['accepted'],
        ], [
            'gdpr_consent.accepted' => __('members.apply.dsgvo.gdpr.required'),
        ]);

        if ($this->application === null) {
            $this->step = 'invalid';

            return;
        }

        $this->application->verified_at = Carbon::now();
        $this->application->gdpr_consent_at = Carbon::now();
        $this->application->newsletter_consent_at = $this->newsletter_consent ? Carbon::now() : null;
        $this->application->photo_consent_at = $this->photo_consent ? Carbon::now() : null;
        $this->application->save();

        $boardMembers = Member::getBoardMembers();

        $boardUsers = $boardMembers
            ->filter(fn (Member $member): bool => $member->user_id !== null)
            ->map(fn (Member $member) => \App\Models\User::find($member->user_id))
            ->filter();

        Notification::send($boardMembers, new NewMemberAppliedNotification($this->application));
        Notification::send($boardUsers, new MemberApplicationVerifiedNotification($this->application));

        $this->step = 'done';
    }

    #[Layout('layouts.guest')]
    public function render(): \Illuminate\View\View
    {
        return view('livewire.member.apply.page')
            ->title(__('welcome.members.apply.header'));
    }
}

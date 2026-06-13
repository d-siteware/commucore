<?php

declare(strict_types=1);

use App\Mail\AcceptMembershipMail;
use App\Models\Membership\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('sends acceptance mail with correct data', function (): void {
    Mail::fake();

    $member = Member::factory()->create();

    Mail::to($member->email)->send(new AcceptMembershipMail($member));

    Mail::assertSent(AcceptMembershipMail::class, function (AcceptMembershipMail $mail) use ($member): bool {
        return $mail->member->id === $member->id
            && $mail->envelope()->subject === __('mails.acceptance.subject');
    });
});

it('mail has correct view', function (): void {
    $member = Member::factory()->create();

    $mail = new AcceptMembershipMail($member);

    expect($mail->content()->view)->toBe('emails.member-acceptance');
});

it('mail has correct from address', function (): void {
    $member = Member::factory()->create();

    $mail = new AcceptMembershipMail($member);

    expect($mail->envelope()->from->address)
        ->toBe(setting('organization.email'));
});

<?php

declare(strict_types=1);

use App\Livewire\Member\Roles\Page;
use App\Models\Membership\Member;
use App\Models\User;

it('only board members can create a new role', function (): void {
    $member = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id]);

    $this->actingAs($member->user)->assertAuthenticated();

    Livewire::test(Page::class)->call('saveMemberRole')->assertOk();

});

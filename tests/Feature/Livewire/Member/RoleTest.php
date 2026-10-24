<?php

declare(strict_types=1);

use App\Livewire\Member\Roles\Page;
use App\Models\Membership\Member;
use App\Models\Membership\Role;
use App\Models\User;

it('only board members can create a new role', function (): void {
    $member = Member::factory()->withUser()->create(['user_id' => User::factory()->create(['email_verified_at' => now()])->id]);

    $this->actingAs($member->user)->assertAuthenticated();

    Livewire::test(Page::class)->call('saveMemberRole')->assertOk();

});

it('assigns a member to a role', function (): void {
    $user = User::factory()->admin()->create();
    $member = Member::factory()->create();
    $role = Role::factory()->create();

    Livewire::actingAs($user)
        ->test(Page::class)
        ->set('memberRoleForm.member_id', $member->id)
        ->set('memberRoleForm.role_id', $role->id)
        ->call('saveMemberRole')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('member_role', [
        'member_id' => $member->id,
        'role_id' => $role->id,
    ]);
});

<?php

declare(strict_types=1);

use App\Models\Membership\Member;
use App\Models\Membership\MemberRole;
use App\Models\Membership\Role;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

describe('MemberRole model', function (): void {
    it('extends Pivot', function (): void {
        $memberRole = new MemberRole;

        expect($memberRole)->toBeInstanceOf(Pivot::class);
    });

    it('has fillable attributes', function (): void {
        $member = Member::factory()->create();
        $role = Role::factory()->create();
        $designatedAt = now()->toDateString();

        $memberRole = MemberRole::create([
            'member_id' => $member->id,
            'role_id' => $role->id,
            'designated_at' => $designatedAt,
            'about_me' => ['de' => 'About me'],
        ]);

        expect($memberRole->member_id)->toBe($member->id)
            ->and($memberRole->role_id)->toBe($role->id)
            ->and($memberRole->designated_at->format('Y-m-d'))->toBe($designatedAt)
            ->and($memberRole->about_me)->toBe(['de' => 'About me']);
    });

    it('belongs to a member', function (): void {
        $member = Member::factory()->create();
        $role = Role::factory()->create();

        $memberRole = MemberRole::create([
            'member_id' => $member->id,
            'role_id' => $role->id,
            'designated_at' => now()->toDateString(),
        ]);

        expect($memberRole->member)->toBeInstanceOf(Member::class)
            ->and($memberRole->member->id)->toBe($member->id);
    });

    it('belongs to a role', function (): void {
        $member = Member::factory()->create();
        $role = Role::factory()->create();

        $memberRole = MemberRole::create([
            'member_id' => $member->id,
            'role_id' => $role->id,
            'designated_at' => now()->toDateString(),
        ]);

        expect($memberRole->role)->toBeInstanceOf(Role::class)
            ->and($memberRole->role->id)->toBe($role->id);
    });

    it('casts designated_at and resigned_at to dates', function (): void {
        $member = Member::factory()->create();
        $role = Role::factory()->create();

        $memberRole = MemberRole::create([
            'member_id' => $member->id,
            'role_id' => $role->id,
            'designated_at' => '2024-01-15',
            'resigned_at' => '2024-06-01',
        ]);

        expect($memberRole->designated_at)->toBeInstanceOf(Carbon::class)
            ->and($memberRole->designated_at->format('Y-m-d'))->toBe('2024-01-15')
            ->and($memberRole->resigned_at)->toBeInstanceOf(Carbon::class)
            ->and($memberRole->resigned_at->format('Y-m-d'))->toBe('2024-06-01');
    });

    it('activeRolePivot returns self when not resigned', function (): void {
        $member = Member::factory()->create();
        $role = Role::factory()->create();

        $memberRole = MemberRole::create([
            'member_id' => $member->id,
            'role_id' => $role->id,
            'designated_at' => now()->toDateString(),
        ]);

        expect($memberRole->activeRolePivot())->toBe($memberRole);
    });

    it('activeRolePivot returns null when resigned', function (): void {
        $member = Member::factory()->create();
        $role = Role::factory()->create();

        $memberRole = MemberRole::create([
            'member_id' => $member->id,
            'role_id' => $role->id,
            'designated_at' => '2024-01-01',
            'resigned_at' => '2024-06-01',
        ]);

        expect($memberRole->activeRolePivot())->toBeNull();
    });
});

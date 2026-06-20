<?php

declare(strict_types=1);

namespace App\Models\Membership;

use App\Models\Concerns\InvalidatesOnboardingStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $member_id
 * @property int $role_id
 * @property \Illuminate\Support\Carbon $designated_at
 * @property \Illuminate\Support\Carbon|null $resigned_at
 * @property string|null $about_me
 * @property string|null $profile_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberRole whereAboutMe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberRole whereDesignatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberRole whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberRole whereProfileImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberRole whereResignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MemberRole whereUpdatedAt($value)
 *
 * @property-read \App\Models\Membership\Member $member
 * @property-read \App\Models\Membership\Role $role
 *
 * @mixin \Eloquent
 */
final class MemberRole extends Pivot
{
    use InvalidatesOnboardingStatus;

    protected $fillable = [
        'member_id',
        'role_id',
        'designated_at',
        'resigned_at',
        'about_me',
        'profile_image',
    ];

    protected $casts = [
        'designated_at' => 'date',
        'resigned_at' => 'date',
        'about_me' => 'array',
    ];

    public function activeRolePivot(): ?MemberRole
    {
        return $this->resigned_at === null ? $this : null;
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}

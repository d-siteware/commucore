<?php

declare(strict_types=1);

namespace App\Models\Protocols\Minutes;

use App\Models\Membership\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $meeting_minute_id
 * @property string $name
 * @property string|null $email
 * @property int|null $member_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Protocols\Minutes\MeetingMinute $meetingMinute
 * @property-read Member|null $member
 *
 * @method static \Database\Factories\Protocols\Minutes\AttendeeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendee query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendee whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendee whereMeetingMinuteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendee whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendee whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class Attendee extends Model
{
    /** @use HasFactory<\Database\Factories\Protocols\Minutes\AttendeeFactory> */
    use HasFactory;

    protected $table = 'attendees';

    protected $guarded = [];

    public function meetingMinute(): BelongsTo
    {
        return $this->belongsTo(MeetingMinute::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}

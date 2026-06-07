<?php

declare(strict_types=1);

namespace App\Models\Event;

use App\Models\Membership\Member;
use App\Models\User;
use Database\Factories\Event\EventTimelineFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property Carbon $start
 * @property int|null $duration
 * @property Carbon $end
 * @property int $event_id
 * @property string $title
 * @property string|null $description
 * @property string|null $notes
 * @property int|null $member_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $place
 * @property string|null $performer
 * @property-read Event $event
 * @property-read Member|null $member
 * @property-read User $user
 *
 * @method static EventTimelineFactory factory($count = null, $state = [])
 * @method static Builder<static>|EventTimeline newModelQuery()
 * @method static Builder<static>|EventTimeline newQuery()
 * @method static Builder<static>|EventTimeline query()
 * @method static Builder<static>|EventTimeline whereCreatedAt($value)
 * @method static Builder<static>|EventTimeline whereDescription($value)
 * @method static Builder<static>|EventTimeline whereDuration($value)
 * @method static Builder<static>|EventTimeline whereEnd($value)
 * @method static Builder<static>|EventTimeline whereEventId($value)
 * @method static Builder<static>|EventTimeline whereId($value)
 * @method static Builder<static>|EventTimeline whereMemberId($value)
 * @method static Builder<static>|EventTimeline whereNotes($value)
 * @method static Builder<static>|EventTimeline wherePerformer($value)
 * @method static Builder<static>|EventTimeline wherePlace($value)
 * @method static Builder<static>|EventTimeline whereStart($value)
 * @method static Builder<static>|EventTimeline whereTitle($value)
 * @method static Builder<static>|EventTimeline whereUpdatedAt($value)
 * @method static Builder<static>|EventTimeline whereUserId($value)
 *
 * @property array<array-key, mixed>|null $title_extern
 *
 * @method static Builder<static>|EventTimeline whereTitleExtern($value)
 *
 * @mixin Eloquent
 */
final class EventTimeline extends Model
{
    /** @use HasFactory<EventTimelineFactory> */
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'title_extern' => 'array',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

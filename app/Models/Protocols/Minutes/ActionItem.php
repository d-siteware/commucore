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
 * @property int $meeting_topic_id
 * @property string $description
 * @property int|null $assignee_id
 * @property \Illuminate\Support\Carbon|null $due_date
 * @property int $completed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Member|null $assignee
 * @property-read \App\Models\Protocols\Minutes\MeetingMinute $meetingMinute
 * @property-read \App\Models\Protocols\Minutes\MeetingTopic $topic
 *
 * @method static \Database\Factories\Protocols\Minutes\ActionItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionItem whereAssigneeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionItem whereCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionItem whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionItem whereMeetingMinuteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionItem whereMeetingTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ActionItem whereUpdatedAt($value)
 *
 * @property-read \App\Models\Protocols\Minutes\MeetingTopic $meetingTopic
 *
 * @mixin \Eloquent
 */
final class ActionItem extends Model
{
    /** @use HasFactory<\Database\Factories\Protocols\Minutes\ActionItemFactory> */
    use HasFactory;

    protected $table = 'action_items';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
        ];
    }

    public function meetingTopic(): BelongsTo
    {
        return $this->belongsTo(MeetingTopic::class, 'meeting_topic_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'assignee_id');
    }

    public function meetingMinute(): BelongsTo
    {
        return $this->belongsTo(MeetingMinute::class, 'meeting_minute_id');
    }
}

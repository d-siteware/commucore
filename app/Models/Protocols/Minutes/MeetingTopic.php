<?php

declare(strict_types=1);

namespace App\Models\Protocols\Minutes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $meeting_id
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Protocols\Minutes\ActionItem> $actionItems
 * @property-read int|null $action_items_count
 * @property-read \App\Models\Protocols\Minutes\MeetingMinute $meetingMinute
 *
 * @method static \Database\Factories\Protocols\Minutes\MeetingTopicFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingTopic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingTopic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingTopic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingTopic whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingTopic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingTopic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingTopic whereMeetingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingTopic whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
final class MeetingTopic extends Model
{
    /** @use HasFactory<\Database\Factories\Protocols\Minutes\MeetingTopicFactory> */
    use HasFactory;

    protected $table = 'meeting_topics';

    protected $guarded = [];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function meetingMinute(): BelongsTo
    {
        return $this->belongsTo(MeetingMinute::class, 'meeting_id');
    }

    public function actionItems(): HasMany
    {
        return $this->hasMany(ActionItem::class, 'meeting_topic_id');
    }
}

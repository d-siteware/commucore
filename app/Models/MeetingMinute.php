<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon $meeting_date
 * @property string|null $location
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ActionItem> $actionItems
 * @property-read int|null $action_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendee> $attendees
 * @property-read int|null $attendees_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MeetingTopic> $topics
 * @property-read int|null $topics_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingMinute newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingMinute newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingMinute query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingMinute whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingMinute whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingMinute whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingMinute whereMeetingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingMinute whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeetingMinute whereUpdatedAt($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\History> $histories
 * @property-read int|null $histories_count
 *
 * @method static \Database\Factories\MeetingMinuteFactory factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
final class MeetingMinute extends Model
{
    use HasFactory;
    use HasHistory;

    protected $table = 'meeting_minutes';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'meeting_date' => 'datetime',
        ];
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(MeetingTopic::class, 'meeting_id');
    }

    public function actionItems(): HasMany
    {
        return $this->hasMany(ActionItem::class, 'meeting_minute_id', 'id');
    }
}

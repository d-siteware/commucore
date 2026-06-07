<?php

declare(strict_types=1);

namespace App\Models\Event;

use App\Enums\AssignmentStatus;
use App\Models\Membership\Member;
use App\Models\User;
use Database\Factories\Event\EventAssignmentFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @method static EventAssignment create(array $attributes)
 *
 * @property int $id
 * @property string $task
 * @property AssignmentStatus $status
 * @property string|null $description
 * @property Carbon|null $due_at
 * @property int|null $amount
 * @property int $event_id
 * @property int $member_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Event $event
 * @property-read Member $member
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read User $user
 *
 * @method static EventAssignmentFactory factory($count = null, $state = [])
 * @method static Builder<static>|EventAssignment newModelQuery()
 * @method static Builder<static>|EventAssignment newQuery()
 * @method static Builder<static>|EventAssignment query()
 * @method static Builder<static>|EventAssignment whereAmount($value)
 * @method static Builder<static>|EventAssignment whereCreatedAt($value)
 * @method static Builder<static>|EventAssignment whereDescription($value)
 * @method static Builder<static>|EventAssignment whereDueAt($value)
 * @method static Builder<static>|EventAssignment whereEventId($value)
 * @method static Builder<static>|EventAssignment whereId($value)
 * @method static Builder<static>|EventAssignment whereMemberId($value)
 * @method static Builder<static>|EventAssignment whereStatus($value)
 * @method static Builder<static>|EventAssignment whereTask($value)
 * @method static Builder<static>|EventAssignment whereUpdatedAt($value)
 * @method static Builder<static>|EventAssignment whereUserId($value)
 *
 * @mixin Eloquent
 */
final class EventAssignment extends Model
{
    /** @use HasFactory<EventAssignmentFactory> */
    use HasFactory;
    use Notifiable;

    protected $guarded = [];

    protected $casts = [
        'due_at' => 'datetime',
        'status' => AssignmentStatus::class,
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

    public function statusColor(): string
    {
        return $this->status->color();
    }

    public function statusLabel(): string
    {
        return $this->status->label();
    }

    public function getDueString(): string
    {
        if ($this->due_at === null) {
            return '-';
        }

        return $this->due_at->isPast() ? $this->due_at->format('Y-m-d') : $this->due_at->diffForHumans();

    }

    public function amountString(): string
    {
        return $this->amount
        ? number_format($this->amount / 100, 2, ',', '.').' EUR'
            : '-,--';
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Membership;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $email
 * @property string $token
 * @property int $accepted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 *
 * @method static Builder<static>|Invitation newModelQuery()
 * @method static Builder<static>|Invitation newQuery()
 * @method static Builder<static>|Invitation query()
 * @method static Builder<static>|Invitation whereAccepted($value)
 * @method static Builder<static>|Invitation whereCreatedAt($value)
 * @method static Builder<static>|Invitation whereEmail($value)
 * @method static Builder<static>|Invitation whereId($value)
 * @method static Builder<static>|Invitation whereToken($value)
 * @method static Builder<static>|Invitation whereUpdatedAt($value)
 * @method static \Database\Factories\Membership\InvitationFactory factory($count = null, $state = [])
 *
 * @mixin Eloquent
 */
final class Invitation extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'email',
        'token',
        'accepted',
    ];

    public function invite(): void {}
}

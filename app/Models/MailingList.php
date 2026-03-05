<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasHistory;
use Database\Factories\MailingListFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $terms_accepted_at
 * @property Carbon|null $unsubscribed_at
 * @property string $name
 * @property string $email
 * @property int|null $member_id
 *
 * @method static MailingListFactory factory($count = null, $state = [])
 * @method static Builder<static>|MailingList newModelQuery()
 * @method static Builder<static>|MailingList newQuery()
 * @method static Builder<static>|MailingList query()
 * @method static Builder<static>|MailingList whereCreatedAt($value)
 * @method static Builder<static>|MailingList whereEmail($value)
 * @method static Builder<static>|MailingList whereId($value)
 * @method static Builder<static>|MailingList whereMemberId($value)
 * @method static Builder<static>|MailingList whereName($value)
 * @method static Builder<static>|MailingList whereUpdatedAt($value)
 *
 * @property bool $terms_accepted
 * @property bool $update_on_events
 * @property bool|null $update_on_articles
 * @property bool|null $update_on_notifications
 * @property Carbon|null $verified_at
 * @property string|null $verification_token
 * @property string|null $locale
 *
 * @method static Builder<static>|MailingList whereLocale($value)
 * @method static Builder<static>|MailingList whereTermsAccepted($value)
 * @method static Builder<static>|MailingList whereUpdateOnArticles($value)
 * @method static Builder<static>|MailingList whereUpdateOnEvents($value)
 * @method static Builder<static>|MailingList whereUpdateOnNotifications($value)
 * @method static Builder<static>|MailingList whereVerificationToken($value)
 * @method static Builder<static>|MailingList whereVerifiedAt($value)
 *
 * @mixin Eloquent
 */
final class MailingList extends Model
{
    /** @use HasFactory<MailingListFactory> */
    use HasFactory;

    use HasHistory;

    protected $fillable = [
        'email',
        'terms_accepted',
        'update_on_events',
        'update_on_articles',
        'update_on_notifications',
        'verified_at',
        'verification_token',
        'locale',
        'terms_accepted_at',
        'unsubscribed_at',
    ];

    protected $hidden = ['verification_token'];

    protected $casts = [
        'verified_at' => 'datetime',
        'terms_accepted' => 'boolean',
        'update_on_events' => 'boolean',
        'update_on_articles' => 'boolean',
        'update_on_notifications' => 'boolean',
        'terms_accepted_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model): void {
            $model->verification_token = Str::random(40); // Generate token on signup
        });
    }

    public function verify(): void
    {
        $this->update([
            'verified_at' => now(),
            'verification_token' => null,
        ]);
    }

    public function generateNewToken(): ?string
    {
        $this->update(['verification_token' => Str::random(40)]);

        return $this->verification_token;
    }

    public function scopeSubscribed(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereNotNull('verified_at')->whereNull('unsubscribed_at');
    }
}

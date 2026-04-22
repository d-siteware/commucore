<?php

declare(strict_types=1);

namespace App\Models\Event;

use App\Enums\EventStatus;
use App\Enums\Locale;
use App\Models\EventAssignment;
use App\Models\EventTimeline;
use App\Models\Traits\HasHistory;
use App\Models\Venue;
use Carbon\Carbon;
use Database\Factories\Event\EventFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @method static Event create(array $attributes)
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $event_date
 * @property Carbon|null $start_time
 * @property Carbon|null $end_time
 * @property array<array-key, mixed> $title
 * @property array<array-key, mixed>|null $slug
 * @property array<array-key, mixed>|null $excerpt
 * @property array<array-key, mixed>|null $description
 * @property string|null $image
 * @property EventStatus $status
 * @property int|null $entry_fee
 * @property int|null $entry_fee_discounted
 * @property int|null $venue_id
 * @property string|null $payment_link
 * @property string|null $name
 * @property-read Collection<int, EventAssignment> $assignments
 * @property-read int|null $assignments_count
 * @property-read Collection<int, EventSubscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read Collection<int, EventTimeline> $timelines
 * @property-read int|null $timelines_count
 * @property-read Venue|null $venue
 * @property-read Collection<int, EventVisitor> $visitors
 * @property-read int|null $visitors_count
 *
 * @method static EventFactory factory($count = null, $state = [])
 * @method static Builder<static>|Event newModelQuery()
 * @method static Builder<static>|Event newQuery()
 * @method static Builder<static>|Event query()
 * @method static Builder<static>|Event whereCreatedAt($value)
 * @method static Builder<static>|Event whereDescription($value)
 * @method static Builder<static>|Event whereEndTime($value)
 * @method static Builder<static>|Event whereEntryFee($value)
 * @method static Builder<static>|Event whereEntryFeeDiscounted($value)
 * @method static Builder<static>|Event whereEventDate($value)
 * @method static Builder<static>|Event whereExcerpt($value)
 * @method static Builder<static>|Event whereId($value)
 * @method static Builder<static>|Event whereImage($value)
 * @method static Builder<static>|Event whereName($value)
 * @method static Builder<static>|Event wherePaymentLink($value)
 * @method static Builder<static>|Event whereSlug($value)
 * @method static Builder<static>|Event whereStartTime($value)
 * @method static Builder<static>|Event whereStatus($value)
 * @method static Builder<static>|Event whereTitle($value)
 * @method static Builder<static>|Event whereUpdatedAt($value)
 * @method static Builder<static>|Event whereVenueId($value)
 *
 * @property-read Collection<int, \App\Models\History> $histories
 * @property-read int|null $histories_count
 * @property-read Collection<int, \App\Models\Blog\Post> $posts
 * @property-read int|null $posts_count
 * @property Carbon|null $notification_sent_at
 *
 * @method static Builder<static>|Event whereNotificationSentAt($value)
 *
 * @mixin Eloquent
 */
final class Event extends Model
{
    use HasFactory;
    use HasHistory;

    protected $fillable = [
        'name',
        'event_date',
        'start_time',
        'end_time',
        'title',
        'slug',
        'excerpt',
        'description',
        'image',
        'status',
        'entry_fee',
        'entry_fee_discounted',
        'venue_id',
    ];

    protected $casts = [
        'name' => 'string',
        'title' => 'array',
        'excerpt' => 'array',
        'slug' => 'array',
        'description' => 'array',
        'notification_sent_at' => 'datetime',
        'entry_fee' => 'integer',
        'entry_fee_discounted' => 'integer',
        'event_date' => 'date',
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'status' => EventStatus::class,
    ];

    /*   public function getRouteKeyName(): string
       {
           return 'slug';
       }*/

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(EventSubscription::class);
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(EventVisitor::class);
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(EventTimeline::class)->orderBy('start');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EventAssignment::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(\App\Models\Blog\Post::class);
    }

    public function relatedPosts(): \Illuminate\Support\Collection
    {
        return $this->posts()->where('status', EventStatus::PUBLISHED->value)->get();
    }

    public function getEmailSubject(string $locale = Locale::HU->value): string
    {
        return trans('event.notification_mail.subject', [], $locale);
    }

    public function hasPoster(string $locale, string $type = 'jpg'): bool
    {
        return Storage::disk('public')->exists('images/posters/'.$this->makePosterName($locale).'.'.$type);
    }

    public function getPoster(string $locale = 'de', string $type = 'jpg'): string
    {
        return Storage::disk('public')->url('images/posters/'.$this->makePosterName($locale).'.'.$type);
    }

    public function getFilename(string $locale = 'de'): string
    {
        return $this->makePosterName($locale);
    }

    public function getPosterSize(string $locale, string $type = 'jpg'): int
    {
        if ($this->hasPoster($locale, $type)) {
            return (int) round(Storage::disk('public')->size('images/posters/'.$this->makePosterName($locale).'.'.$type) / 1024, 0);
        }

        return 0;
    }

    public function location(): string
    {
        return $this->venue->name.', '.$this->venue->address.', '.$this->venue->city;
    }

    protected function makePosterName(string $locale = 'de'): string
    {
        if (empty($this->title[$locale])) {
            return 'poster';
        }

        return Str::slug($this->title[$locale]).'_poster';
    }
}

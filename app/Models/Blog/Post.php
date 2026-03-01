<?php

declare(strict_types=1);

namespace App\Models\Blog;

use App\Models\User;
use Database\Factories\Blog\PostFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property array<array-key, mixed> $title
 * @property array<array-key, mixed> $slug
 * @property array<array-key, mixed> $body
 * @property int $user_id
 * @property string $status
 * @property int $post_type_id
 * @property string|null $label
 * @property Carbon|null $published_at
 * @property-read Collection<int, PostImage> $images
 * @property-read int|null $images_count
 * @property-read PostType|null $type
 * @property-read User $user
 *
 * @method static PostFactory factory($count = null, $state = [])
 * @method static Builder<static>|Post newModelQuery()
 * @method static Builder<static>|Post newQuery()
 * @method static Builder<static>|Post query()
 * @method static Builder<static>|Post whereBody($value)
 * @method static Builder<static>|Post whereCreatedAt($value)
 * @method static Builder<static>|Post whereId($value)
 * @method static Builder<static>|Post whereLabel($value)
 * @method static Builder<static>|Post wherePostTypeId($value)
 * @method static Builder<static>|Post wherePublishedAt($value)
 * @method static Builder<static>|Post whereSlug($value)
 * @method static Builder<static>|Post whereStatus($value)
 * @method static Builder<static>|Post whereTitle($value)
 * @method static Builder<static>|Post whereUpdatedAt($value)
 * @method static Builder<static>|Post whereUserId($value)
 *
 * @property int|null $event_id
 * @property-read \App\Models\Event\Event|null $event
 *
 * @method static Builder<static>|Post whereEventId($value)
 *
 * @mixin Eloquent
 */
final class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'user_id',
        'status',
        'label',
        'published_at',
        'post_type_id',
        'event_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'title' => 'array',
        'slug' => 'array',
        'body' => 'array',
    ];

    public function isPublished(): bool
    {
        return $this->status === 'published' || $this->published_at !== null;

    }

    public function status_color(): string
    {
        return $this->type->color;
    }

    public function typeColor(): ?string
    {
        return $this->type->color;
    }

    public function type(): HasOne
    {
        return $this->hasOne(PostType::class, 'id', 'post_type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function excerpt($limit = 100): string
    {
        $text = preg_replace('/<\/?(p|div|br|li|h[1-6])\b[^>]*>/', ' ', $this->body[app()->getLocale()]);
        $excerpt = trim(strip_tags($text));

        return Str::limit($excerpt, $limit, ' ...', true);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Event\Event::class);
    }

    public function getEmailSubject(string $locale): string
    {
        return trans('post.notification_mail.subject', [], $locale);
    }
}

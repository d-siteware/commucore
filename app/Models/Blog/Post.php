<?php

declare(strict_types=1);

namespace App\Models\Blog;

use App\Enums\EventStatus;
use App\Models\Event\Event;
use App\Models\Project\Project;
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
 * @property EventStatus|null $status
 * @property int $post_type_id
 * @property string|null $label
 * @property Carbon|null $published_at
 * @property int|null $event_id
 * @property int|null $project_id
 * @property-read Collection<int, PostImage> $images
 * @property-read int|null $images_count
 * @property-read PostType|null $type
 * @property-read User $user
 * @property-read Event|null $event
 * @property-read Project|null $project
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
 * @method static Builder<static>|Post whereEventId($value)
 * @method static Builder<static>|Post whereProjectId($value)
 * @method static Builder<static>|Post forEvent(int $eventId)
 * @method static Builder<static>|Post forProject(int $projectId)
 * @method static Builder<static>|Post standalone()
 * @method static Builder<static>|Post published()
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
        'project_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'title' => 'array',
        'slug' => 'array',
        'body' => 'array',
        'status' => EventStatus::class,
    ];

    // ==================== Relationships ====================

    public function type(): HasOne
    {
        return $this->hasOne(PostType::class, 'id', 'post_type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PostImage::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // ==================== Scopes ====================

    public function scopeForEvent(Builder $query, int $eventId): Builder
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Posts die weder einem Event noch einem Projekt zugeordnet sind.
     */
    public function scopeStandalone(Builder $query): Builder
    {
        return $query->whereNull('event_id')->whereNull('project_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    // ==================== Methods ====================

    public function isPublished(): bool
    {
        return $this->status === EventStatus::PUBLISHED;
    }

    /**
     * Post ist keinem Event und keinem Projekt zugeordnet.
     */
    public function isStandalone(): bool
    {
        return $this->event_id === null && $this->project_id === null;
    }

    /**
     * Gibt den Kontext-Titel zurück (Event- oder Projekttitel).
     * Nützlich für Listen-Darstellungen.
     */
    public function contextTitle(string $locale): ?string
    {
        if ($this->event !== null) {
            $title = $this->event->title;

            return $title[$locale] ?? (reset($title) ?: null);
        }

        if ($this->project !== null) {
            return $this->project->title;
        }

        return null;
    }

    public function status_color(): string
    {
        return $this->type->color;
    }

    public function typeColor(): ?string
    {
        return $this->type->color;
    }

    public function excerpt(int $limit = 100): string
    {
        $locale = app()->getLocale();
        $text = preg_replace('/<\/?(p|div|br|li|h[1-6])\b[^>]*>/', ' ', $this->body[$locale] ?? '');
        $excerpt = trim(strip_tags((string) $text));

        return Str::limit($excerpt, $limit, ' ...', true);
    }

    public function getEmailSubject(string $locale): string
    {
        return trans('post.notification_mail.subject', [], $locale);
    }
}

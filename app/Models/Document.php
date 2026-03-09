<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\HasHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $documentable_type
 * @property int $documentable_id
 * @property int $uploaded_by_user_id
 * @property string $uuid
 * @property string $original_name
 * @property string $disk
 * @property string $path
 * @property string $mime_type
 * @property int $size
 * @property string|null $category
 * @property string|null $label
 * @property string|null $notes
 * @property Carbon|null $last_accessed_at
 * @property int|null $last_accessed_by_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Model $documentable
 * @property-read User $uploadedBy
 * @property-read User|null $lastAccessedBy
 *
 * @method static Builder<static>|Document query()
 * @method static Builder<static>|Document whereDocumentableType(string $type)
 * @method static Builder<static>|Document whereDocumentableId(int $id)
 *
 * @mixin \Eloquent
 */
class Document extends Model
{
    use HasHistory;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'last_accessed_at' => 'datetime',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function lastAccessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_accessed_by_user_id');
    }

    // =========================================================================
    // Storage
    // =========================================================================

    public function storagePath(): string
    {
        return $this->path;
    }

    public function storageExists(): bool
    {
        return Storage::disk($this->disk)->exists($this->path);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    public function fileSizeForHumans(): string
    {
        $bytes = $this->size;

        return match (true) {
            $bytes >= 1_048_576 => number_format($bytes / 1_048_576, 2).' MB',
            $bytes >= 1_024 => number_format($bytes / 1_024, 2).' KB',
            default => $bytes.' B',
        };
    }

    /**
     * Icon-Name basierend auf MIME-Type (für Heroicons).
     */
    public function icon(): string
    {
        return match (true) {
            str_contains($this->mime_type, 'pdf') => 'document-text',
            str_contains($this->mime_type, 'image') => 'photo',
            str_contains($this->mime_type, 'word') => 'document',
            str_contains($this->mime_type, 'excel') => 'table-cells',
            str_contains($this->mime_type, 'rfc822'),
            str_contains($this->original_name, '.eml') => 'envelope',
            default => 'paper-clip',
        };
    }

    // =========================================================================
    // Audit
    // =========================================================================

    public function recordAccess(User $user): void
    {
        $this->updateQuietly([
            'last_accessed_at' => now(),
            'last_accessed_by_user_id' => $user->id,
        ]);
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeNeverAccessed(Builder $query): Builder
    {
        return $query->whereNull('last_accessed_at');
    }
}

<?php

declare(strict_types=1);

namespace App\Models\Membership;

use App\Enums\MemberDocumentCategory;
use App\Models\Traits\HasHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $member_id
 * @property int $uploaded_by_user_id
 * @property string $uuid
 * @property string $original_name
 * @property string $disk
 * @property string $path
 * @property string $mime_type
 * @property int $size
 * @property MemberDocumentCategory $category
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $last_accessed_at
 * @property int|null $last_accessed_by_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Member $member
 * @property-read User $uploadedBy
 * @property-read User|null $lastAccessedBy
 *
 * @method static Builder<static>|MemberDocument query()
 * @method static Builder<static>|MemberDocument whereMemberId($value)
 * @method static Builder<static>|MemberDocument whereCategory($value)
 *
 * @mixin \Eloquent
 */
final class MemberDocument extends Model
{
    //    use HasFactory;
    use HasHistory;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'category' => MemberDocumentCategory::class,
        'last_accessed_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationen
    // -------------------------------------------------------------------------

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function lastAccessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_accessed_by_user_id');
    }

    // -------------------------------------------------------------------------
    // Storage
    // -------------------------------------------------------------------------

    /**
     * Datei aus dem privaten Storage streamen.
     * Nie direkt die URL ausgeben – immer durch den Controller schleusen.
     */
    public function storagePath(): string
    {
        return $this->path;
    }

    public function storageExists(): bool
    {
        return Storage::disk($this->disk)->exists($this->path);
    }

    public function fileSizeForHumans(): string
    {
        $bytes = $this->size;

        return match (true) {
            $bytes >= 1_048_576 => number_format($bytes / 1_048_576, 2).' MB',
            $bytes >= 1_024 => number_format($bytes / 1_024, 2).' KB',
            default => $bytes.' B',
        };
    }

    // -------------------------------------------------------------------------
    // Audit
    // -------------------------------------------------------------------------

    /**
     * Zugriff protokollieren – wird im Controller nach jedem Download aufgerufen.
     */
    public function recordAccess(User $user): void
    {
        $this->updateQuietly([
            'last_accessed_at' => now(),
            'last_accessed_by_user_id' => $user->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeOfCategory(Builder $query, MemberDocumentCategory $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeNeverAccessed(Builder $query): Builder
    {
        return $query->whereNull('last_accessed_at');
    }
}

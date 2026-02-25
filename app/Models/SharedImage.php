<?php

declare(strict_types=1);

// app/Models/SharedImage.php

namespace App\Models;

use App\Models\Membership\Invitation;
use Database\Factories\SharedImageFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string|null $guest_token
 * @property string $filename
 * @property string $original_filename
 * @property string|null $caption
 * @property string $author
 * @property string|null $alt_text
 * @property int|null $file_size
 * @property string|null $mime_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $approver
 * @property-read Invitation|null $invitation
 * @property-read User|null $user
 *
 * @method static SharedImageFactory factory($count = null, $state = [])
 * @method static Builder<static>|SharedImage newModelQuery()
 * @method static Builder<static>|SharedImage newQuery()
 * @method static Builder<static>|SharedImage query()
 * @method static Builder<static>|SharedImage whereAltText($value)
 * @method static Builder<static>|SharedImage whereAuthor($value)
 * @method static Builder<static>|SharedImage whereCaption($value)
 * @method static Builder<static>|SharedImage whereCreatedAt($value)
 * @method static Builder<static>|SharedImage whereFileSize($value)
 * @method static Builder<static>|SharedImage whereFilename($value)
 * @method static Builder<static>|SharedImage whereGuestToken($value)
 * @method static Builder<static>|SharedImage whereId($value)
 * @method static Builder<static>|SharedImage whereMimeType($value)
 * @method static Builder<static>|SharedImage whereOriginalFilename($value)
 * @method static Builder<static>|SharedImage whereUpdatedAt($value)
 * @method static Builder<static>|SharedImage whereUserId($value)
 *
 * @property string $label
 * @property int|null $invitation_id
 * @property string $path
 * @property string|null $thumbnail_path
 * @property bool $is_approved
 * @property int|null $approved_by
 * @property Carbon|null $approved_at
 * @property array<array-key, mixed>|null $dimensions
 * @property-read Invitation|null $invited
 *
 * @method static Builder<static>|SharedImage whereApprovedAt($value)
 * @method static Builder<static>|SharedImage whereApprovedBy($value)
 * @method static Builder<static>|SharedImage whereDimensions($value)
 * @method static Builder<static>|SharedImage whereInvitationId($value)
 * @method static Builder<static>|SharedImage whereIsApproved($value)
 * @method static Builder<static>|SharedImage whereLabel($value)
 * @method static Builder<static>|SharedImage wherePath($value)
 * @method static Builder<static>|SharedImage whereThumbnailPath($value)
 *
 * @mixin Eloquent
 */
final class SharedImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'user_id',
        'invitation_id',
        'path',
        'thumbnail_path',
        'is_approved',
        'approved_by',
        'approved_at',
        'file_size',
        'dimensions',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'dimensions' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getAuthorAttribute(): string
    {
        return $this->user->name ?? $this->invitation?->email;
    }

    public function imageUrl(): string
    {
        return Storage::disk('local')->url($this->path);
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail_path
            ? Storage::disk('local')->url($this->thumbnail_path)
            : null;
    }

    public function invited(): BelongsTo
    {
        return $this->belongsTo(Invitation::class, 'invitation_id');
    }
}

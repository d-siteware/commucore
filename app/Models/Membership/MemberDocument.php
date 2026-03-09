<?php

declare(strict_types=1);

namespace App\Models\Membership;

use App\Enums\MemberDocumentCategory;
use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Alias-Model für Rückwärtskompatibilität.
 * Alle Daten liegen in der documents-Tabelle.
 *
 * @property int $id
 * @property int $member_id (= documentable_id, via Scope gesetzt)
 * @property MemberDocumentCategory|null $category
 * @property-read Member $member
 *
 * @method static Builder<static>|MemberDocument query()
 * @method static Builder<static>|MemberDocument whereMemberId(int $id)
 *
 * @mixin \Eloquent
 */
class MemberDocument extends Document
{
    protected $table = 'documents';

    protected $casts = [
        'category' => MemberDocumentCategory::class,
        'last_accessed_at' => 'datetime',
    ];

    // =========================================================================
    // Boot – automatisch auf Member-Scope einschränken
    // =========================================================================

    protected static function booted(): void
    {
        static::addGlobalScope('member_documents', function (Builder $query): void {
            $query->where('documentable_type', Member::class);
        });

        static::creating(function (MemberDocument $doc): void {
            $doc->documentable_type = Member::class;
        });
    }

    // =========================================================================
    // Convenience: member_id als Proxy auf documentable_id
    // =========================================================================

    public function getMemberIdAttribute(): int
    {
        return $this->documentable_id;
    }

    public function setMemberIdAttribute(int $value): void
    {
        $this->documentable_id = $value;
    }

    // =========================================================================
    // Relationship
    // =========================================================================

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'documentable_id');
    }

    // =========================================================================
    // Scope (für alte Queries die whereMemberId nutzen)
    // =========================================================================

    public function scopeWhereMemberId(Builder $query, int $memberId): Builder
    {
        return $query->where('documentable_id', $memberId);
    }

    public function scopeOfCategory(Builder $query, MemberDocumentCategory $category): Builder
    {
        return $query->where('category', $category->value);
    }
}

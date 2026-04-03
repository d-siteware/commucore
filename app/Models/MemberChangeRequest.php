<?php

namespace App\Models;

use App\Enums\MemberChangeField;
use App\Models\Membership\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberChangeRequest extends Model
{
    /** @use HasFactory<\Database\Factories\MemberChangeRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'field',
        'member_id',
        'old_value',
        'requested_value',
        'reason',
        'reviewed_by',
        'reviewed_at',
        'completed_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'field' => MemberChangeField::class,
        'reviewed_at' => 'datetime',
        'completed_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /** @return BelongsTo<Member, $this> */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /** @return BelongsTo<User, $this> */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return is_null($this->completed_at) && is_null($this->rejected_at);
    }

    public function statusLabel(): string
    {
        if ($this->completed_at) {
            return __('change_request.status.completed');
        }
        if ($this->rejected_at) {
            return __('change_request.status.rejected');
        }

        return __('change_request.status.pending');
    }

    public function statusColor(): string
    {
        if ($this->completed_at) {
            return 'lime';
        }
        if ($this->rejected_at) {
            return 'red';
        }

        return 'yellow';
    }
}

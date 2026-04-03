<?php

namespace App\Models;

use App\Models\Membership\Member;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberCancellationRequest extends Model
{
    /** @use HasFactory<\Database\Factories\MemberCancellationRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'reviewed_by',
        'reviewed_at',
        'rejected_at',
        'rejection_reason',
        'confirmed_at',
        'member_id',
        'requested_leave_date',
        'reason',
    ];

    protected $casts = [
        'requested_leave_date' => 'date',
        'reviewed_at' => 'datetime',
        'confirmed_at' => 'datetime',
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
        return is_null($this->confirmed_at) && is_null($this->rejected_at);
    }

    public function statusLabel(): string
    {
        if ($this->confirmed_at) {
            return __('cancellation_request.status.confirmed');
        }
        if ($this->rejected_at) {
            return __('cancellation_request.status.rejected');
        }

        return __('cancellation_request.status.pending');
    }

    public function statusColor(): string
    {
        if ($this->confirmed_at) {
            return 'lime';
        }
        if ($this->rejected_at) {
            return 'red';
        }

        return 'yellow';
    }
}

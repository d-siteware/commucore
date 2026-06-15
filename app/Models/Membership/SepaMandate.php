<?php

declare(strict_types=1);

namespace App\Models\Membership;

use App\Enums\SepaMandateStatus;
use App\Enums\SepaMandateType;
use App\Models\Document;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $member_id
 * @property string $mandate_reference
 * @property string $iban
 * @property string|null $bic
 * @property string $account_holder
 * @property Carbon $mandate_date
 * @property SepaMandateType $mandate_type
 * @property SepaMandateStatus $status
 * @property int|null $signed_document_id
 * @property Carbon|null $last_used_at
 * @property Carbon|null $payment_completed_at
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Member $member
 * @property-read Document|null $signedDocument
 *
 * @method static Builder<static>|SepaMandate newModelQuery()
 * @method static Builder<static>|SepaMandate newQuery()
 * @method static Builder<static>|SepaMandate query()
 * @method static Builder<static>|SepaMandate active()
 * @method static Builder<static>|SepaMandate whereStatus($value)
 *
 * @mixin Eloquent
 */
final class SepaMandate extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'mandate_date' => 'date',
        'last_used_at' => 'datetime',
        'payment_completed_at' => 'datetime',
        'mandate_type' => SepaMandateType::class,
        'status' => SepaMandateStatus::class,
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function signedDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'signed_document_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', SepaMandateStatus::Active)
            ->whereNull('payment_completed_at');
    }

    public function isUsable(): bool
    {
        return $this->status === SepaMandateStatus::Active
            && $this->payment_completed_at === null;
    }

    public function markAsUsed(): void
    {
        $this->updateQuietly(['last_used_at' => now()]);
    }

    public function markPaymentCompleted(): void
    {
        $this->update(['payment_completed_at' => now()]);
    }

    public function cancel(): void
    {
        $this->update(['status' => SepaMandateStatus::Cancelled]);
    }

    public static function generateReference(Member $member): string
    {
        $prefix = 'SEPA-';
        $memberId = $member->id;
        $timestamp = now()->format('YmdHis');
        $raw = "{$prefix}{$memberId}-{$timestamp}";

        return Str::limit($raw, 35, '');
    }

    public static function generateIbanHash(string $iban): string
    {
        return '*****'.substr($iban, -4);
    }
}

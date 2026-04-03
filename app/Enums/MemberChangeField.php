<?php

namespace App\Enums;

use App\Models\MemberChangeRequest;

enum MemberChangeField: string
{
    case TYPE = 'type';
    case FEE_TYPE = 'fee_type';
    //   case DEDUCTION_REASON = 'deduction_reason';

    public function label(): string
    {
        return match ($this) {
            self::TYPE => __('members.type.label'),
            self::FEE_TYPE => __('members.fee-type.label'),
            //          self::DEDUCTION_REASON => __('members.apply.discount.reason.label'),
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }

    public function hasOpenRequest(int $memberId): bool
    {
        return MemberChangeRequest::query()
            ->where('member_id', $memberId)
            ->where('field', $this->value)
            ->whereNull('completed_at')
            ->whereNull('rejected_at')
            ->exists();
    }
}

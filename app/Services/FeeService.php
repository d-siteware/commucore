<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\FeeInterval;
use App\Enums\MemberFeeType;
use App\Enums\MembershipFee;
use App\Models\Membership\Member;
use Carbon\Carbon;

class FeeService
{
    public function __construct(
        private readonly SettingsService $settings,
    ) {}

    // -------------------------------------------------------------------------
    // Amounts
    // -------------------------------------------------------------------------

    /**
     * Configured amount in cents for a given fee type.
     * Falls back to the hardcoded MembershipFee enum values so
     * existing instances without settings records never crash.
     */
    public function getAmountForType(MemberFeeType $type): int
    {
        return match ($type) {
            MemberFeeType::FREE => 0,
            MemberFeeType::FULL => (int) $this->settings->get(
                'fees.full_amount',
                MembershipFee::FULL->value,
            ),
            MemberFeeType::DISC => (int) $this->settings->get(
                'fees.discounted_amount',
                MembershipFee::DISCOUNTED->value,
            ),
        };
    }

    /**
     * Convenience wrapper that resolves the MemberFeeType from a Member model.
     * Mirrors the previous MembershipFee::getFeeFromMember() logic.
     */
    public function getAmountForMember(Member $member): int
    {
        $type = $member->fee_type ?? ($member->is_deducted
            ? MemberFeeType::DISC
            : MemberFeeType::FULL);

        return $this->getAmountForType($type);
    }

    public function getPeriodsPerYear(): int
    {
        return match ($this->getInterval()) {
            FeeInterval::MONTHLY => 12,
            FeeInterval::QUARTERLY => 4,
            FeeInterval::BIANNUAL => 2,
            FeeInterval::YEARLY => 1,
            FeeInterval::CUSTOM => $this->getCustomPeriodsPerYear(),
        };
    }

    private function getCustomPeriodsPerYear(): int
    {
        $n = $this->getCustomIntervalN();
        $unit = $this->getCustomIntervalUnit();

        $daysPerYear = 365;
        $daysPerPeriod = match ($unit) {
            'd' => $n,
            'm' => $n * 30,
            'y' => $n * 365,
            default => -1,
        };

        return (int) max(1, round($daysPerYear / $daysPerPeriod));
    }

    // -------------------------------------------------------------------------
    // Interval
    // -------------------------------------------------------------------------

    /**
     * The configured global payment interval for all fee types.
     */
    public function getInterval(): FeeInterval
    {
        $raw = $this->settings->get('fees.interval', FeeInterval::YEARLY->value);

        return FeeInterval::tryFrom((string) $raw) ?? FeeInterval::YEARLY;
    }

    /**
     * For CUSTOM intervals: how many units between payments.
     */
    public function getCustomIntervalN(): int
    {
        return max(1, (int) $this->settings->get('fees.interval_n', 1));
    }

    /**
     * For CUSTOM intervals: the unit — 'd' (day), 'm' (month), 'y' (year).
     */
    public function getCustomIntervalUnit(): string
    {
        $unit = (string) $this->settings->get('fees.interval_unit', 'y');

        return in_array($unit, ['d', 'm', 'y'], true) ? $unit : 'y';
    }

    // -------------------------------------------------------------------------
    // Due date calculation
    // -------------------------------------------------------------------------

    /**
     * Calculate the next due date from a reference point.
     *
     * @param  Carbon  $from  Usually the last payment date or membership start.
     */
    public function getNextDueDate(Carbon $from): Carbon
    {
        $interval = $this->getInterval();
        $carbonInterval = $interval->toCarbonInterval();

        if ($carbonInterval !== null) {
            [$unit, $n] = $carbonInterval;

            return $from->copy()->add($unit, $n);
        }

        // CUSTOM
        $n = $this->getCustomIntervalN();
        $unit = match ($this->getCustomIntervalUnit()) {
            'd' => 'day',
            'm' => 'month',
            default => 'year',
        };

        return $from->copy()->add($unit, $n);
    }

    /**
     * Calculate all due dates between two dates (e.g. for a billing preview).
     *
     * @return Carbon[]
     */
    public function getDueDatesBetween(Carbon $from, Carbon $until): array
    {
        $dates = [];
        $current = $from->copy();

        while ($current->lessThanOrEqualTo($until)) {
            $dates[] = $current->copy();
            $current = $this->getNextDueDate($current);
        }

        return $dates;
    }

    // -------------------------------------------------------------------------
    // Persist settings
    // -------------------------------------------------------------------------

    /**
     * Persist all fee settings at once (called from the Livewire settings form).
     *
     * @param array{
     *     full_amount: int,
     *     discounted_amount: int,
     *     interval: string,
     *     interval_n?: int,
     *     interval_unit?: string,
     * } $data
     */
    public function saveSettings(array $data): void
    {
        $this->settings->set('fees.full_amount', (int) $data['full_amount'], 'integer');
        $this->settings->set('fees.discounted_amount', (int) $data['discounted_amount'], 'integer');
        $this->settings->set('fees.interval', (string) $data['interval'], 'string');

        if ($data['interval'] === FeeInterval::CUSTOM->value) {
            $this->settings->set('fees.interval_n', (int) ($data['interval_n'] ?? 1), 'integer');
            $this->settings->set('fees.interval_unit', (string) ($data['interval_unit'] ?? 'y'), 'string');
        }
    }
}

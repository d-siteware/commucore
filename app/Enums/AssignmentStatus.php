<?php

declare(strict_types=1);

namespace App\Enums;

enum AssignmentStatus: string
{
    case draft = 'draft';
    case pending = 'pending';
    case confirmed = 'confirmed';
    case rejected = 'rejected';
    case completed = 'completed';
    case postponed = 'postponed';

    /**
     * Get the translated label for this status
     */
    public function label(): string
    {
        return match ($this) {
            self::draft => __('assignment.status.draft'),
            self::pending => __('assignment.status.pending'),
            self::confirmed => __('assignment.status.confirmed'),
            self::rejected => __('assignment.status.rejected'),
            self::completed => __('assignment.status.completed'),
            self::postponed => __('assignment.status.postponed'),
        };
    }

    /**
     * Get the color for this status
     */
    public function color(): string
    {
        return match ($this) {
            self::draft => 'zinc',
            self::pending => 'pink',
            self::confirmed => 'lime',
            self::rejected => 'red',
            self::completed => 'emerald',
            self::postponed => 'orange',
        };
    }

    /**
     * Check if status is draft
     */
    public function isDraft(): bool
    {
        return $this === self::draft;
    }

    /**
     * Check if status is pending
     */
    public function isPending(): bool
    {
        return $this === self::pending;
    }

    /**
     * Check if status is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this === self::confirmed;
    }

    /**
     * Check if status is rejected
     */
    public function isRejected(): bool
    {
        return $this === self::rejected;
    }

    /**
     * Check if status is completed
     */
    public function isCompleted(): bool
    {
        return $this === self::completed;
    }

    /**
     * Check if status is postponed
     */
    public function isPostponed(): bool
    {
        return $this === self::postponed;
    }

    /**
     * Check if status is finalized (completed or rejected)
     */
    public function isFinalized(): bool
    {
        return in_array($this, [self::completed, self::rejected], true);
    }

    /**
     * Get all values as array (for dropdowns, filters, etc.)
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all cases with labels (for dropdowns)
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function (self $status): array {
            return [$status->value => $status->label()];
        })->toArray();
    }
}

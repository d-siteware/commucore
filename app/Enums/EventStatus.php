<?php

declare(strict_types=1);

namespace App\Enums;

enum EventStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case PUBLISHED = 'published';
    case REJECTED = 'rejected';
    case RETRACTED = 'retracted';

    /**
     * Get the translated label for this status
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => __('event.status.draft'),
            self::PENDING => __('event.status.pending'),
            self::PUBLISHED => __('event.status.published'),
            self::REJECTED => __('event.status.rejected'),
            self::RETRACTED => __('event.status.retracted'),
        };
    }

    /**
     * Get the color for this status
     */
    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PENDING => 'teal',
            self::PUBLISHED => 'lime',
            self::REJECTED => 'yellow',
            self::RETRACTED => 'orange',
        };
    }

    /**
     * Check if status is draft
     */
    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }

    /**
     * Check if status is pending
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if status is published
     */
    public function isPublished(): bool
    {
        return $this === self::PUBLISHED;
    }

    /**
     * Check if status is rejected
     */
    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }

    /**
     * Check if status is retracted
     */
    public function isRetracted(): bool
    {
        return $this === self::RETRACTED;
    }

    /**
     * Check if event is visible to public
     */
    public function isPubliclyVisible(): bool
    {
        return $this === self::PUBLISHED;
    }

    /**
     * Check if event can be edited
     */
    public function isEditable(): bool
    {
        return in_array($this, [self::DRAFT, self::REJECTED], true);
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
        return collect(self::cases())->mapWithKeys(function (self $status) {
            return [$status->value => $status->label()];
        })->toArray();
    }
}

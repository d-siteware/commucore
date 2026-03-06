<?php

declare(strict_types=1);

namespace App\Enums;

enum ReportStatus: string
{
    case draft = 'draft';
    case submitted = 'submitted';
    case audited = 'audited';

    /**
     * Get the translated label for this report status
     */
    public function label(): string
    {
        return match ($this) {
            self::draft => __('reports.status.draft'),
            self::submitted => __('reports.status.submitted'),
            self::audited => __('reports.status.audited'),
        };
    }

    /**
     * Get the color for this report status
     */
    public function color(): string
    {
        return match ($this) {
            self::draft => 'pink',
            self::submitted => 'gray',
            self::audited => 'lime',
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
     * Check if status is submitted
     */
    public function isSubmitted(): bool
    {
        return $this === self::submitted;
    }

    /**
     * Check if status is audited
     */
    public function isAudited(): bool
    {
        return $this === self::audited;
    }

    /**
     * Check if report is editable
     */
    public function isEditable(): bool
    {
        return $this === self::draft;
    }

    /**
     * Check if report is finalized
     */
    public function isFinalized(): bool
    {
        return $this === self::audited;
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

<?php

namespace App\Enums;

enum BookingAccountArea: string
{
    case IDEAL = 'ideal';
    case ASSET_MANAGEMENT = 'asset_management';
    case PURPOSE_OPERATION = 'purpose_operation';
    case ECONOMIC_BUSINESS = 'economic_business';

    /**
     * Return label
     */
    public function label(): string
    {
        return match ($this) {
            self::IDEAL => __('account.area.ideal.label'),
            self::ASSET_MANAGEMENT => __('account.area.asset_management.label'),
            self::PURPOSE_OPERATION => __('account.area.purpose_operation.label'),
            self::ECONOMIC_BUSINESS => __('account.area.economic_business.label'),
        };
    }

    /**
     *  Description
     */
    public function description(): string
    {
        return match ($this) {
            self::IDEAL => __('account.area.ideal.description'),
            self::ASSET_MANAGEMENT => __('account.area.asset_management.description'),
            self::PURPOSE_OPERATION => __('account.area.purpose_operation.description'),
            self::ECONOMIC_BUSINESS => __('account.area.economic_business.description'),
        };
    }
}

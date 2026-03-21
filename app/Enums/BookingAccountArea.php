<?php

namespace App\Enums;

enum BookingAccountArea: string
{
    case IDEAL = 'ideal';
    case ASSET_MANAGEMENT = 'asset_management';
    case PURPOSE_OPERATION = 'purpose_operation';
    case ECONOMIC_BUSINESS = 'economic_business';

    /**
     * Numerischer KOST1-Wert für den DATEV-Export (SKR42).
     *
     * DATEV erwartet im Feld "KOST1 - Kostenstelle" einen numerischen Wert:
     *   1 = Ideeller Bereich
     *   2 = Vermögensverwaltung
     *   3 = Zweckbetrieb
     *   4 = Wirtschaftlicher Geschäftsbetrieb
     *
     * @see https://developer.datev.de/datev/platform/de/dtvf/formate/buchungsstapel
     */
    public function datevKost1(): string
    {
        return match ($this) {
            self::IDEAL => '1',
            self::ASSET_MANAGEMENT => '2',
            self::PURPOSE_OPERATION => '3',
            self::ECONOMIC_BUSINESS => '4',
        };
    }

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
     * Description
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

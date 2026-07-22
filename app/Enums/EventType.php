<?php

declare(strict_types=1);

namespace App\Enums;

enum EventType: string
{
    case GENERAL_MEETING = 'general_meeting';
    case CELEBRATION = 'celebration';
    case WORKSHOP = 'workshop';
    case SPORT = 'sport';
    case BOARD_MEETING = 'board_meeting';
    case TRIP = 'trip';
    case CHARITY = 'charity';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::GENERAL_MEETING => __('event.type.general_meeting'),
            self::CELEBRATION => __('event.type.celebration'),
            self::WORKSHOP => __('event.type.workshop'),
            self::SPORT => __('event.type.sport'),
            self::BOARD_MEETING => __('event.type.board_meeting'),
            self::TRIP => __('event.type.trip'),
            self::CHARITY => __('event.type.charity'),
            self::OTHER => __('event.type.other'),
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $type) => [
            $type->value => $type->label(),
        ])->toArray();
    }
}

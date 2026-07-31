<?php

namespace App\Enums;

enum SpaceType: string
{
    case EventSpace = 'event_space';
    case Leasing = 'leasing';

    public function label(): string
    {
        return match ($this) {
            self::EventSpace => 'Event space',
            self::Leasing => 'Leasing',
        };
    }
}

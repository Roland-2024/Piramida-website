<?php

namespace App\Enums;

enum EventCategory: string
{
    case Event = 'event';
    case Exhibition = 'exhibition';
    case GuidedTour = 'guided_tour';

    public function label(): string
    {
        return match ($this) {
            self::Event => 'Event',
            self::Exhibition => 'Exhibition',
            self::GuidedTour => 'Guided tour',
        };
    }
}

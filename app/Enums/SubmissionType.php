<?php

namespace App\Enums;

enum SubmissionType: string
{
    case Contact = 'contact';
    case EventRegistration = 'event_registration';
    case ProgramApplication = 'program_application';
    case SpaceBooking = 'space_booking';
    case Leasing = 'leasing';
    case CareerApplication = 'career_application';

    public function label(): string
    {
        return match ($this) {
            self::Contact => 'Contact',
            self::EventRegistration => 'Event registration',
            self::ProgramApplication => 'Program application',
            self::SpaceBooking => 'Event space request',
            self::Leasing => 'Leasing enquiry',
            self::CareerApplication => 'Career application',
        };
    }
}

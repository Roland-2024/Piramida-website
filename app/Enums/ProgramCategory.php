<?php

namespace App\Enums;

enum ProgramCategory: string
{
    case Education = 'education';
    case Innovation = 'innovation';
    case Business = 'business';
    case ArtCulture = 'art_culture';

    public function label(): string
    {
        return match ($this) {
            self::Education => 'Education',
            self::Innovation => 'Innovation',
            self::Business => 'Business',
            self::ArtCulture => 'Art & Culture',
        };
    }
}

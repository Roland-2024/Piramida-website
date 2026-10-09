<?php

namespace App\Enums;

enum BusinessCategory: string
{
    case Education = 'education';
    case Innovation = 'innovation';
    case Business = 'business';
    case ArtCulture = 'art_culture';
    case SocialSpaces = 'social_spaces';

    public function label(): string
    {
        return match ($this) {
            self::Education => 'Education',
            self::Innovation => 'Innovation',
            self::Business => 'Business',
            self::ArtCulture => 'Art & Culture',
            self::SocialSpaces => 'Social Spaces',
        };
    }
}

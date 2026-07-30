<?php

namespace App\Enums;

enum SectionType: string
{
    case Hero = 'hero';
    case TextImage = 'text_image';
    case Features = 'features';
    case Statistics = 'statistics';
    case Gallery = 'gallery';
    case CallToAction = 'call_to_action';
    case Partners = 'partners';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Hero => 'Hero',
            self::TextImage => 'Text and image',
            self::Features => 'Features',
            self::Statistics => 'Statistics',
            self::Gallery => 'Gallery',
            self::CallToAction => 'Call to action',
            self::Partners => 'Partners',
            self::Custom => 'Custom content',
        };
    }
}

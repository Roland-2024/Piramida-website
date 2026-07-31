<?php

namespace App\Enums;

enum BusinessCategory: string
{
    case Cafe = 'cafe';
    case Restaurant = 'restaurant';
    case Technology = 'technology';
    case Art = 'art';

    public function label(): string
    {
        return match ($this) {
            self::Cafe => 'Café',
            self::Restaurant => 'Restaurant',
            self::Technology => 'Technology',
            self::Art => 'Art',
        };
    }
}

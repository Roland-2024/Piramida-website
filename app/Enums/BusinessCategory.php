<?php

namespace App\Enums;

enum BusinessCategory: string
{
    case Cafe = 'cafe';
    case Restaurant = 'restaurant';
    case Shop = 'shop';
    case Technology = 'technology';
    case Art = 'art';

    public function label(): string
    {
        return match ($this) {
            self::Cafe => 'Café',
            self::Restaurant => 'Restaurant',
            self::Shop => 'Shop',
            self::Technology => 'Technology',
            self::Art => 'Art',
        };
    }
}

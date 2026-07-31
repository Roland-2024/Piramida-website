<?php

namespace App\Enums;

enum BookingMode: string
{
    case None = 'none';
    case Internal = 'internal';
    case External = 'external';
    case Both = 'both';

    public function label(): string
    {
        return match ($this) {
            self::None => 'No booking',
            self::Internal => 'Internal request form',
            self::External => 'External link',
            self::Both => 'Internal form and external link',
        };
    }

    public function allowsInternal(): bool
    {
        return $this === self::Internal || $this === self::Both;
    }

    public function allowsExternal(): bool
    {
        return $this === self::External || $this === self::Both;
    }
}

<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case New = 'new';
    case InReview = 'in_review';
    case Replied = 'replied';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::InReview => 'In review',
            self::Replied => 'Replied',
            self::Closed => 'Closed',
        };
    }
}

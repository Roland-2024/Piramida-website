<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || preg_match('/[\x00-\x1F\x7F]/', $value)) {
            $fail('The :attribute must be a safe URL.');

            return;
        }

        if (str_starts_with($value, '/') && ! str_starts_with($value, '//')) {
            return;
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);

        if (in_array($scheme, ['http', 'https'], true) && filter_var($value, FILTER_VALIDATE_URL)) {
            return;
        }

        $fail('The :attribute must be an internal path or an HTTP(S) URL.');
    }
}

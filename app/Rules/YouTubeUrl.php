<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class YouTubeUrl implements ValidationRule
{
    public static function videoId(?string $url): ?string
    {
        if (! $url || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($url);
        if (($parts['scheme'] ?? '') !== 'https' || isset($parts['user']) || isset($parts['pass']) || isset($parts['port'])) {
            return null;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';
        $id = null;
        if ($host === 'youtu.be') {
            $id = ltrim($path, '/');
        } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)) {
            if ($path === '/watch') {
                parse_str($parts['query'] ?? '', $query);
                $id = $query['v'] ?? null;
            } elseif (preg_match('~^/(?:embed|shorts)/([^/]+)$~', $path, $matches)) {
                $id = $matches[1];
            }
        }

        return is_string($id) && preg_match('/^[a-zA-Z0-9_-]{11}$/D', $id) ? $id : null;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || self::videoId($value) === null) {
            $fail('Enter a valid HTTPS YouTube video link.');
        }
    }
}

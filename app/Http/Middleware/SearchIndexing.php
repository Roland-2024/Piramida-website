<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SearchIndexing
{
    public static function allowed(Request $request): bool
    {
        return config('seo.indexable') && $request->getHost() === parse_url(config('seo.url'), PHP_URL_HOST);
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        if (! self::allowed($request) || $request->is('admin', 'admin/*', 'login', 'forgot-password', 'reset-password/*') || $response->getStatusCode() >= 400) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $locale = (string) ($route->parameter('locale') ?? 'al');

        if ($locale === 'al' && $route->hasParameter('locale') && $request->isMethodSafe()) {
            $path = preg_replace('#^/al(?=/|$)#', '', $request->getPathInfo()) ?: '/';
            $query = $request->getQueryString();

            return redirect()->to($request->root().($path === '/' ? '' : $path).($query ? '?'.$query : ''), 301);
        }

        if (! $route->hasParameter('locale')) {
            // Controller arguments are positional: locale must precede slug/floor.
            $parameters = $route->parameters();
            foreach ($parameters as $key => $value) {
                $route->forgetParameter($key);
            }
            $route->setParameter('locale', $locale);
            foreach ($parameters as $key => $value) {
                $route->setParameter($key, $value);
            }
        }

        abort_unless(array_key_exists($locale, config('cms.locales')), 404);

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}

@php
    // Unmatched routes may never run SetLocale middleware.
    $locale = request()->segment(1) === 'en' ? 'en' : 'al';
    $home = url($locale === 'en' ? '/en' : '/');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale === 'al' ? 'sq' : 'en' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>404 · {{ __('errors.not_found', [], $locale) }} · Piramida</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="{{ asset('template/css/404.css') }}?v={{ filemtime(public_path('template/css/404.css')) }}">
</head>
<body>
    <a class="skip-link" href="#main-content">{{ __('errors.skip_content', [], $locale) }}</a>
    <header class="error-header">
        <a href="{{ $home }}" aria-label="{{ __('errors.home', [], $locale) }}">
            <img src="{{ asset('template/images/logo piramida.svg') }}" width="96" height="56" alt="Piramida">
        </a>
        <nav aria-label="{{ __('errors.language', [], $locale) }}">
            <a href="{{ url('/') }}" lang="sq" hreflang="sq">AL</a>
            <a href="{{ url('/en') }}" lang="en" hreflang="en">EN</a>
        </nav>
    </header>
    <main id="main-content" tabindex="-1">
        <p class="error-code" aria-hidden="true">404</p>
        <p class="error-label">{{ __('errors.label', [], $locale) }}</p>
        <h1>{{ __('errors.not_found', [], $locale) }}</h1>
        <p class="error-description">{{ __('errors.description', [], $locale) }}</p>
        <a class="home-button" href="{{ $home }}">{{ __('errors.home', [], $locale) }} <span aria-hidden="true">↗</span></a>
    </main>
    <footer>PIRAMIDA · TIRANA</footer>
</body>
</html>

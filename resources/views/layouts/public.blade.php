@php
    $pageUrl = function (string $slug) use ($headerPages, $footerPages) {
        $page = $headerPages->concat($footerPages)->first(fn ($page) => $page->translations->contains('slug', $slug));
        $translated = $page?->translation(app()->getLocale(), false);
        return $translated ? route('public.pages.show', [app()->getLocale(), $translated->slug]) : route('public.home', app()->getLocale());
    };
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'al' ? 'sq' : 'en' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}Piramida</title>
    @if ($description)<meta name="description" content="{{ $description }}">@endif
    @vite(['resources/css/public.css', 'resources/js/public.js'])
    @foreach (array_unique(array_merge(['theme', 'header', 'footer'], $styles)) as $style)
        <link rel="stylesheet" href="{{ asset('template/css/'.$style.'.css').'?v='.filemtime(public_path('template/css/'.$style.'.css')) }}">
    @endforeach
    <link rel="stylesheet" href="{{ asset('template/integration.css').'?v='.filemtime(public_path('template/integration.css')) }}">
</head>
<body id="top" class="{{ $bodyClass }}">
    <a href="#main-content" class="skip-link">{{ __('cms.skip_content') }}</a>
    @include('public.partials.header')
    <main id="main-content" tabindex="-1">{{ $slot }}</main>
    @if ($footer) @include('public.partials.footer') @endif
</body>
</html>

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
    <link rel="icon" href="{{ \App\Support\WebsiteContent::image('favicon.svg') }}">
    <title>{{ $seo['title'] }}</title>
    <meta name="description" content="{{ $seo['description'] }}">
    <link rel="canonical" href="{{ $seo['canonical'] }}">
    <link rel="describedby" href="{{ \App\Support\Seo::url('/llms.txt') }}" type="text/plain">
    @foreach($seo['alternates'] as $language =>$url)<link rel="alternate" hreflang="{{ $language }}" href="{{ $url }}">
    @endforeach
    @if(isset($seo['alternates']['sq']))<link rel="alternate" hreflang="x-default" href="{{ $seo['alternates']['sq'] }}">@endif
    <meta property="og:type" content="{{ $seo['type'] }}">
    <meta property="og:site_name" content="{{ __('seo.name') }}">
    <meta property="og:title" content="{{ $seo['title'] }}">
    <meta property="og:description" content="{{ $seo['description'] }}">
    <meta property="og:url" content="{{ $seo['canonical'] }}">
    <meta property="og:locale" content="{{ $seo['language'] === 'sq' ? 'sq_AL' : 'en_GB' }}">
    @foreach($seo['alternates'] as $language => $url)
        @if($language !== $seo['language'])<meta property="og:locale:alternate" content="{{ $language === 'sq' ? 'sq_AL' : 'en_GB' }}">@endif
    @endforeach
    <meta property="og:image" content="{{ $seo['image'] }}">
    <meta property="og:image:alt" content="{{ $seo['imageAlt'] }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['title'] }}">
    <meta name="twitter:description" content="{{ $seo['description'] }}">
    <meta name="twitter:image" content="{{ $seo['image'] }}">
    <meta name="twitter:image:alt" content="{{ $seo['imageAlt'] }}">
    <script type="application/ld+json">{!! json_encode(['@context' => 'https://schema.org', '@graph' => $seo['graph']], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
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

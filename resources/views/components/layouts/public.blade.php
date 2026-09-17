@props(['title' => null, 'description' => null, 'languageUrls' => [], 'styles' => [], 'bodyClass' => 'desktop-page-background', 'footer' => true])

@include('layouts.public', [
    'title' => $title,
    'description' => $description,
    'languageUrls' => $languageUrls,
    'slot' => $slot,
    'styles' => $styles,
    'bodyClass' => $bodyClass,
    'footer' => $footer,
])

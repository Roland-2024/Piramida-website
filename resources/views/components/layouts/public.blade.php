@props(['title' => null, 'description' => null, 'record' => null, 'languageUrls' => [], 'styles' => [], 'bodyClass' => 'desktop-page-background', 'footer' => true])

@include('layouts.public', [
    'title' => $title,
    'seo' => \App\Support\Seo::metadata($record, $title, $description, $languageUrls),
    'description' => $description,
    'languageUrls' => $languageUrls,
    'slot' => $slot,
    'styles' => $styles,
    'bodyClass' => $bodyClass,
    'footer' => $footer,
])

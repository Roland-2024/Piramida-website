@props(['title' => null, 'description' => null, 'languageUrls' => []])

@include('layouts.public', [
    'title' => $title,
    'description' => $description,
    'languageUrls' => $languageUrls,
    'slot' => $slot,
])

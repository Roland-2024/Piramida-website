@props(['media' => null, 'path' => null, 'src' => null, 'sizes' => '100vw', 'loading' => 'eager'])
@php
    $url = $media?->displayUrl() ?? ($path ? \App\Support\WebsiteContent::image($path) : $src);
    $srcset = $media ? $media->srcset() : ($path ? \App\Support\WebsiteContent::imageSrcset($path) : null);
    if ($media?->width && $media?->height) {
        $attributes = $attributes->merge(['width' => $media->width, 'height' => $media->height]);
    }
@endphp
<img src="{{ $url }}" @if($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif loading="{{ $loading }}" decoding="async" {{ $attributes }}>

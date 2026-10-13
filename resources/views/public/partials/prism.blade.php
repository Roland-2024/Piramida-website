@if ($media)
    <div class="prism"><x-public-image :media="$media" alt="{{ app()->getLocale() === 'en' ? $media->alt_text_en : $media->alt_text_al }}" loading="lazy" sizes="(max-width: 639px) 100vw, 50vw" /></div>
@endif

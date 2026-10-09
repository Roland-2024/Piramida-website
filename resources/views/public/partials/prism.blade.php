@if ($media)
    <div class="prism"><img src="{{ $media->displayUrl() }}" alt="{{ app()->getLocale() === 'en' ? $media->alt_text_en : $media->alt_text_al }}" loading="lazy"></div>
@endif

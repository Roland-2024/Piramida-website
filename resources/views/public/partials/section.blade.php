@php
    $sectionTranslation = $section->translation(app()->getLocale());
    $items = data_get($section->structured_data, app()->getLocale().'.items', []);
    $imageAlt = fn ($media) => app()->getLocale() === 'en' ? $media->alt_text_en : $media->alt_text_al;
    $isPillars = ($homepage ?? false) && $section->type === \App\Enums\SectionType::Features;
    $isPartners = $section->type === \App\Enums\SectionType::Partners;
@endphp
<section class="page-section">
    <div class="grid items-center gap-10 {{ $section->primaryMedia ? 'lg:grid-cols-2' : '' }}">
        @if ($section->primaryMedia)
            <div>
                @include('public.partials.prism', ['media' => $section->primaryMedia])
                @if ($section->secondaryMedia)<img src="{{ $section->secondaryMedia->url() }}" alt="{{ $imageAlt($section->secondaryMedia) }}" class="mt-5 max-h-80 w-full rounded-xl object-cover" loading="lazy">@endif
            </div>
        @endif
        <div class="{{ $isPillars || $isPartners ? 'mx-auto max-w-2xl text-center' : '' }}">
            @if ($sectionTranslation?->subtitle)<p class="eyebrow">{{ $sectionTranslation->subtitle }}</p>@endif
            @if ($sectionTranslation?->title)<h2>{{ $sectionTranslation->title }}</h2>@endif
            @if ($sectionTranslation?->description)<div class="prose-content mt-5">{!! $sectionTranslation->description !!}</div>@endif
            <div class="mt-6 flex flex-wrap gap-3">
                @if ($section->primary_button_url && $sectionTranslation?->primary_button_label)<a href="{{ $section->primary_button_url }}" class="public-button">{{ $sectionTranslation->primary_button_label }}</a>@endif
                @if ($section->secondary_button_url && $sectionTranslation?->secondary_button_label)<a href="{{ $section->secondary_button_url }}" class="outline-button">{{ $sectionTranslation->secondary_button_label }}</a>@endif
            </div>
        </div>
    </div>
    @if ($items)
        <div class="{{ $isPillars ? 'pillar-grid mt-8' : 'feature-list' }}">
            @foreach ($items as $item)
                <article class="{{ $isPillars ? 'pillar-card' : '' }}">
                    @if ($isPillars && $loop->index < 4)
                        @php $artwork = ['education', 'innovation', 'business', 'art'][$loop->index]; @endphp
                        <img src="{{ asset('template/images/'.$artwork.'.png') }}" alt="" class="pillar-photo" loading="lazy">
                        <img src="{{ asset('template/images/'.ucfirst($artwork).'.svg') }}" alt="" width="26" height="30">
                    @endif
                    <div><h3>{{ data_get($item, 'title') }}</h3>@if (data_get($item, 'text'))<p class="mt-3">{{ data_get($item, 'text') }}</p>@endif</div>
                </article>
            @endforeach
        </div>
    @endif
    @if ($section->video_url)
        <div class="mt-8 overflow-hidden rounded-xl">
            @if (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $section->video_url))
                <video controls preload="metadata" @if($section->primaryMedia) poster="{{ $section->primaryMedia->url() }}" @endif class="aspect-video w-full"><source src="{{ $section->video_url }}"></video>
            @else
                <a href="{{ $section->video_url }}" target="_blank" rel="noopener" class="public-button">{{ __('cms.play_video') }} ↗</a>
            @endif
        </div>
    @endif
    @if ($section->gallery->isNotEmpty())
        <div class="mt-8 {{ $isPartners ? 'grid grid-cols-2 items-center gap-6 sm:grid-cols-3' : 'carousel-track' }}">
            @foreach ($section->gallery as $media)
                <img src="{{ $media->url() }}" alt="{{ $imageAlt($media) }}" loading="lazy" class="{{ $isPartners ? 'h-24 w-full bg-white p-4 object-contain' : 'h-80 rounded-xl object-cover' }}">
            @endforeach
        </div>
    @endif
</section>

@php
    $title = data_get($translation, $translationTitleColumn);
    $bookingMode = data_get($item, 'booking_mode');
@endphp

<x-layouts.public :title="$translation->seo_title ?: $title" :description="$translation->seo_description ?: $translation->short_description" :language-urls="$languageUrls">
    <article class="site-shell py-16">
        @if (isset($item->category) && $item->category instanceof \BackedEnum)<p class="text-sm font-semibold uppercase accent">{{ $item->category->label() }}</p>@endif
        @if (isset($item->type) && $item->type instanceof \BackedEnum)<p class="text-sm font-semibold uppercase accent">{{ $item->type->label() }}</p>@endif
        <h1 class="mt-3 public-title">{{ $title }}</h1>
        @if ($translation->location)<p class="mt-4 text-lg muted">{{ $translation->location }}</p>@endif
        @if (method_exists($item, 'featuredMedia'))<div class="mt-10 max-w-2xl">@include('public.partials.prism', ['media' => $item->featuredMedia])</div>@endif
        @if (method_exists($item, 'gallery') && $item->gallery->isNotEmpty())
            <div class="carousel-track mt-8">
                @foreach ($item->gallery as $media)<img src="{{ $media->url() }}" alt="{{ app()->getLocale() === 'en' ? $media->alt_text_en : $media->alt_text_al }}" loading="lazy" class="h-80 rounded-xl object-cover">@endforeach
            </div>
        @endif
        <div class="prose-content mt-10 ">{!! $translation->description !!}</div>
        @if ($translation->requirements)<div class="prose-content mt-8 border-t border-white/20 pt-6"><h2 class="mb-3 text-xl font-semibold">{{ __('cms.requirements') }}</h2>{!! $translation->requirements !!}</div>@endif
        @if ($translation->features)<div class="prose-content mt-8 border-t border-white/20 pt-6"><h2 class="mb-3 text-xl font-semibold">{{ __('cms.features') }}</h2>{!! $translation->features !!}</div>@endif

        @if ($bookingMode?->allowsExternal() && $item->external_url)
            <a href="{{ $item->external_url }}" target="_blank" rel="noopener" class="public-button mt-8">{{ __('cms.external_form') }}</a>
        @endif

        @if ($bookingMode?->allowsInternal())
            @php
                $isCareer = $item instanceof \App\Models\Career;
                $isLeasing = $item instanceof \App\Models\Space && $item->type === \App\Enums\SpaceType::Leasing;
                $action = $isCareer
                    ? route('public.careers.apply', [app()->getLocale(), $translation->slug])
                    : route(
                        $isLeasing ? 'public.spaces.leasing-request' : 'public.spaces.event-request',
                        [app()->getLocale(), $translation->slug],
                    );
                $formType = $isCareer ? 'career' : ($isLeasing ? 'leasing' : 'event_space');
            @endphp
            <section class="request-panel mt-12">
                <h2 class="text-2xl font-semibold">{{ $isCareer ? __('cms.apply') : __('cms.send_request') }}</h2>
                <p class="mt-2 text-sm text-slate-500">{{ __('cms.request_confirmation_notice') }}</p>
                @include('public.submissions._form', [
                    'action' => $action,
                    'formType' => $formType,
                ])
            </section>
        @endif
    </article>
</x-layouts.public>

@php
    $sectionTranslation = $section->translation(app()->getLocale());
    $items = data_get($section->structured_data, app()->getLocale().'.items', []);
    $imageAlt = fn ($media) => app()->getLocale() === 'en' ? $media->alt_text_en : $media->alt_text_al;
@endphp

<section class="py-10">
    <div class="grid items-center gap-8 {{ $section->primaryMedia ? 'lg:grid-cols-2' : '' }}">
        <div>
            @if ($sectionTranslation?->subtitle)<p class="mb-2 text-sm font-semibold uppercase tracking-widest text-amber-700">{{ $sectionTranslation->subtitle }}</p>@endif
            @if ($sectionTranslation?->title)<h2 class="text-3xl font-semibold tracking-tight sm:text-4xl">{{ $sectionTranslation->title }}</h2>@endif
            @if ($sectionTranslation?->description)<div class="prose-content mt-5 text-slate-600">{!! $sectionTranslation->description !!}</div>@endif
            @if ($section->primary_button_url || $section->secondary_button_url)
                <div class="mt-6 flex flex-wrap gap-3">
                    @if ($section->primary_button_url && $sectionTranslation?->primary_button_label)<a href="{{ $section->primary_button_url }}" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">{{ $sectionTranslation->primary_button_label }}</a>@endif
                    @if ($section->secondary_button_url && $sectionTranslation?->secondary_button_label)<a href="{{ $section->secondary_button_url }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold">{{ $sectionTranslation->secondary_button_label }}</a>@endif
                </div>
            @endif
        </div>
        @if ($section->primaryMedia)
            <div class="grid gap-4 {{ $section->secondaryMedia ? 'sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2' : '' }}">
                <img src="{{ $section->primaryMedia->url() }}" alt="{{ $imageAlt($section->primaryMedia) }}" class="h-full max-h-[30rem] w-full rounded-3xl object-cover">
                @if ($section->secondaryMedia)
                    <img src="{{ $section->secondaryMedia->url() }}" alt="{{ $imageAlt($section->secondaryMedia) }}" class="h-full max-h-[30rem] w-full rounded-3xl object-cover">
                @endif
            </div>
        @endif
    </div>

    @if ($items)
        <div class="mt-8 grid gap-4 {{ $section->type === \App\Enums\SectionType::Partners ? 'sm:grid-cols-3' : 'sm:grid-cols-2 lg:grid-cols-4' }}">
            @foreach ($items as $item)
                <article class="rounded-2xl border border-slate-200 bg-white p-5">
                    <h3 class="font-semibold text-slate-950">{{ data_get($item, 'title') }}</h3>
                    @if (data_get($item, 'text'))<p class="mt-2 text-sm leading-6 text-slate-600">{{ data_get($item, 'text') }}</p>@endif
                </article>
            @endforeach
        </div>
    @endif

    @if ($section->video_url)
        <div class="mt-8 overflow-hidden rounded-3xl bg-slate-950">
            @if (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $section->video_url))
                <video controls preload="metadata" @if($section->primaryMedia) poster="{{ $section->primaryMedia->url() }}" @endif class="aspect-video w-full">
                    <source src="{{ $section->video_url }}">
                </video>
            @else
                <a href="{{ $section->video_url }}" target="_blank" rel="noopener" class="flex aspect-video items-center justify-center bg-slate-900 text-lg font-semibold text-white">
                    <span class="rounded-full bg-lime-300 px-6 py-4 text-slate-950">Play video</span>
                </a>
            @endif
        </div>
    @endif

    @if ($section->gallery->isNotEmpty())
        <div class="mt-8 grid gap-4 {{ $section->type === \App\Enums\SectionType::Partners ? 'grid-cols-2 items-center sm:grid-cols-3' : 'sm:grid-cols-2 lg:grid-cols-3' }}">
            @foreach ($section->gallery as $media)
                <div class="{{ $section->type === \App\Enums\SectionType::Partners ? 'rounded-2xl border border-slate-200 bg-white p-6' : '' }}">
                    <img src="{{ $media->url() }}" alt="{{ $imageAlt($media) }}" class="{{ $section->type === \App\Enums\SectionType::Partners ? 'mx-auto h-20 w-full object-contain' : 'h-64 w-full rounded-2xl object-cover' }}">
                </div>
            @endforeach
        </div>
    @endif
</section>

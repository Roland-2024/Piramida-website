@php $sectionTranslation = $section->translation(app()->getLocale()); @endphp

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
            <img src="{{ $section->primaryMedia->url() }}" alt="{{ app()->getLocale() === 'en' ? $section->primaryMedia->alt_text_en : $section->primaryMedia->alt_text_al }}" class="max-h-[30rem] w-full rounded-3xl object-cover">
        @endif
    </div>
</section>

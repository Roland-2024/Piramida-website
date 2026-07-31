@php $translation = $page?->translation(app()->getLocale()); @endphp

<x-layouts.public :title="$translation?->seo_title ?: $translation?->title" :description="$translation?->seo_description ?: $translation?->short_description" :language-urls="$languageUrls">
    @if ($page)
        <section class="bg-slate-950 text-white">
            <div class="mx-auto grid max-w-7xl items-center gap-10 px-5 py-16 lg:grid-cols-2 lg:px-8 lg:py-24">
                <div>
                    <p class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-amber-400">Piramida</p>
                    <h1 class="text-4xl font-semibold tracking-tight sm:text-6xl">{{ $translation?->title }}</h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">{{ $translation?->short_description }}</p>
                </div>
                @if ($page->featuredMedia)<img src="{{ $page->featuredMedia->url() }}" alt="{{ app()->getLocale() === 'en' ? $page->featuredMedia->alt_text_en : $page->featuredMedia->alt_text_al }}" class="max-h-[32rem] w-full rounded-3xl object-cover">@endif
            </div>
        </section>

        <div class="mx-auto max-w-7xl divide-y divide-slate-200 px-5 lg:px-8">
            @foreach ($page->sections as $section) @include('public.partials.section', ['section' => $section]) @endforeach
        </div>
    @else
        <section class="mx-auto max-w-7xl px-5 py-24 text-center lg:px-8"><h1 class="text-4xl font-semibold">Piramida</h1><p class="mt-4 text-slate-500">{{ __('cms.no_content') }}</p></section>
    @endif

    <section class="bg-slate-950 text-white">
        <div class="mx-auto max-w-7xl px-5 py-14 lg:px-8">
            <div class="flex items-end justify-between gap-4"><div><p class="text-sm font-semibold uppercase tracking-widest text-lime-300">{{ __('cms.attractions') }}</p><h2 class="mt-2 text-3xl font-semibold">{{ __('cms.step_into_piramida') }}</h2></div><a href="{{ route('public.attractions.index', app()->getLocale()) }}" class="text-sm font-semibold text-lime-300">{{ __('cms.read_more') }} →</a></div>
            <div class="mt-7 grid gap-6 md:grid-cols-2">
                @foreach ($featuredAttractions as $attraction)
                    @php $item = $attraction->translation(app()->getLocale(), false); @endphp
                    <article class="overflow-hidden rounded-3xl border border-white/10 bg-white/5">
                        @if ($attraction->featuredMedia)<img src="{{ $attraction->featuredMedia->url() }}" alt="{{ app()->getLocale() === 'en' ? $attraction->featuredMedia->alt_text_en : $attraction->featuredMedia->alt_text_al }}" class="h-64 w-full object-cover">@endif
                        <div class="p-6"><h3 class="text-2xl font-semibold">{{ $item?->title }}</h3><p class="mt-3 text-sm leading-6 text-slate-300">{{ $item?->short_description }}</p><a href="{{ route('public.attractions.show', [app()->getLocale(), $item?->slug]) }}" class="mt-5 inline-block text-sm font-semibold text-lime-300">{{ __('cms.read_more') }}</a></div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-14 lg:px-8">
        <div class="flex items-end justify-between gap-4"><div><p class="text-sm font-semibold uppercase tracking-widest text-amber-700">{{ __('cms.experiences') }}</p><h2 class="mt-2 text-3xl font-semibold">{{ __('cms.explore_corners') }}</h2></div><a href="{{ route('public.businesses.index', app()->getLocale()) }}" class="text-sm font-semibold text-amber-700">{{ __('cms.read_more') }} →</a></div>
        <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($featuredBusinesses as $business)
                @php $item = $business->translation(app()->getLocale(), false); @endphp
                <a href="{{ route('public.businesses.index', app()->getLocale()) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    @if ($business->featuredMedia)<img src="{{ $business->featuredMedia->url() }}" alt="{{ app()->getLocale() === 'en' ? $business->featuredMedia->alt_text_en : $business->featuredMedia->alt_text_al }}" class="h-64 w-full object-cover transition duration-300 group-hover:scale-105">@endif
                    <div class="p-5"><h3 class="text-xl font-semibold">{{ $item?->name }}</h3><p class="mt-1 text-sm text-slate-500">{{ $business->category->label() }}</p></div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-14 lg:px-8">
        <div class="flex items-end justify-between gap-4"><div><p class="text-sm font-semibold uppercase tracking-widest text-amber-700">{{ __('cms.news') }}</p><h2 class="mt-2 text-3xl font-semibold">{{ __('cms.latest') }}</h2></div><a href="{{ route('public.news.index', app()->getLocale()) }}" class="text-sm font-semibold text-amber-700">{{ __('cms.read_more') }} →</a></div>
        <div class="mt-7 grid gap-5 md:grid-cols-3">
            @forelse ($latestNews as $article) @php $item = $article->translation(app()->getLocale()); @endphp
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">@if ($article->featuredMedia)<img src="{{ $article->featuredMedia->url() }}" alt="" class="h-48 w-full object-cover">@endif<div class="p-5"><p class="text-xs text-slate-500">{{ $article->published_at->format('d M Y') }}</p><h3 class="mt-3 text-xl font-semibold">{{ $item?->title }}</h3><p class="mt-3 text-sm text-slate-600">{{ $item?->excerpt }}</p><a href="{{ route('public.news.show', [app()->getLocale(), $item?->slug]) }}" class="mt-5 inline-block text-sm font-semibold text-amber-700">{{ __('cms.read_more') }}</a></div></article>
            @empty <p class="text-sm text-slate-500">{{ __('cms.no_content') }}</p> @endforelse
        </div>
    </section>

    <section class="bg-amber-100">
        <div class="mx-auto max-w-7xl px-5 py-14 lg:px-8"><div class="flex items-end justify-between gap-4"><h2 class="text-3xl font-semibold">{{ __('cms.events') }}</h2><a href="{{ route('public.events.index', app()->getLocale()) }}" class="text-sm font-semibold">{{ __('cms.read_more') }} →</a></div><div class="mt-7 grid gap-5 md:grid-cols-3">@forelse ($upcomingEvents as $event) @php $item = $event->translation(app()->getLocale()); @endphp<article class="overflow-hidden rounded-2xl bg-white">@if ($event->featuredMedia)<img src="{{ $event->featuredMedia->url() }}" alt="" class="h-56 w-full object-cover">@endif<div class="p-5"><p class="text-xs font-semibold uppercase text-amber-700">{{ $event->starts_at->format('d M Y · H:i') }}</p><h3 class="mt-3 text-xl font-semibold">{{ $item?->title }}</h3><p class="mt-2 text-sm text-slate-500">{{ $item?->location }}</p><a href="{{ route('public.events.show', [app()->getLocale(), $item?->slug]) }}" class="mt-5 inline-block text-sm font-semibold text-amber-700">{{ __('cms.read_more') }}</a></div></article>@empty<p class="text-sm text-slate-500">{{ __('cms.no_content') }}</p>@endforelse</div></div>
    </section>
</x-layouts.public>

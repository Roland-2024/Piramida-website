<x-layouts.public :title="__('cms.spaces')" :language-urls="$languageUrls">
    <section class="bg-slate-950 py-16 text-white">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-lime-300">{{ __('cms.rent_a_space') }}</p>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">{{ $spaceType === \App\Enums\SpaceType::EventSpace ? __('cms.event_spaces') : __('cms.leasing') }}</h1>
            <p class="mt-4 max-w-3xl text-slate-300">{{ $spaceType === \App\Enums\SpaceType::EventSpace ? __('cms.event_spaces_intro') : __('cms.leasing_intro') }}</p>
            <nav class="mt-8 flex flex-wrap gap-3" aria-label="{{ __('cms.space_types') }}">
                <a href="{{ route('public.spaces.index', [app()->getLocale(), 'type' => \App\Enums\SpaceType::EventSpace->value]) }}" class="rounded-full px-5 py-2.5 text-sm font-semibold {{ $spaceType === \App\Enums\SpaceType::EventSpace ? 'bg-lime-300 text-slate-950' : 'border border-white/30' }}">{{ __('cms.event_spaces') }}</a>
                <a href="{{ route('public.spaces.index', [app()->getLocale(), 'type' => \App\Enums\SpaceType::Leasing->value]) }}" class="rounded-full px-5 py-2.5 text-sm font-semibold {{ $spaceType === \App\Enums\SpaceType::Leasing ? 'bg-lime-300 text-slate-950' : 'border border-white/30' }}">{{ __('cms.leasing') }}</a>
            </nav>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
        <div class="grid gap-6 md:grid-cols-2">
            @forelse ($items as $space)
                @php $translation = $space->translation(app()->getLocale(), false); @endphp
                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                    @if ($space->featuredMedia)
                        <img src="{{ $space->featuredMedia->url() }}" alt="{{ app()->getLocale() === 'en' ? $space->featuredMedia->alt_text_en : $space->featuredMedia->alt_text_al }}" class="h-72 w-full object-cover">
                    @endif
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold">{{ $translation?->title }}</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $translation?->short_description }}</p>
                        <dl class="mt-5 flex flex-wrap gap-2 text-xs">
                            @if ($space->capacity)<div class="rounded-full bg-slate-100 px-3 py-2"><dt class="inline font-semibold">{{ __('cms.capacity') }}:</dt> <dd class="inline">{{ $space->capacity }}</dd></div>@endif
                            @if ($space->area_sqm)<div class="rounded-full bg-slate-100 px-3 py-2"><dt class="inline font-semibold">{{ __('cms.area') }}:</dt> <dd class="inline">{{ $space->area_sqm + 0 }} m²</dd></div>@endif
                            @if ($translation?->location)<div class="rounded-full bg-slate-100 px-3 py-2"><dt class="sr-only">{{ __('cms.location') }}</dt><dd>{{ $translation->location }}</dd></div>@endif
                        </dl>
                        <a href="{{ route('public.spaces.show', [app()->getLocale(), $translation?->slug]) }}" class="mt-6 inline-block rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">{{ $spaceType === \App\Enums\SpaceType::EventSpace ? __('cms.book_now') : __('cms.request_information') }}</a>
                    </div>
                </article>
            @empty
                <p class="text-slate-500">{{ __('cms.no_content') }}</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $items->links() }}</div>
    </section>
</x-layouts.public>

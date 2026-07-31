<x-layouts.public :title="$plural" :language-urls="$languageUrls">
    <section class="mx-auto max-w-7xl px-5 py-16 lg:px-8">
        <h1 class="text-4xl font-semibold">{{ $plural }}</h1>
        <div class="mt-9 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($items as $item)
                @php
                    $translation = $item->translation(app()->getLocale(), false);
                    $title = data_get($translation, $translationTitleColumn);
                @endphp
                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                    @if (method_exists($item, 'featuredMedia') && $item->featuredMedia)
                        <img src="{{ $item->featuredMedia->url() }}" alt="{{ app()->getLocale() === 'en' ? $item->featuredMedia->alt_text_en : $item->featuredMedia->alt_text_al }}" class="h-52 w-full object-cover">
                    @endif
                    <div class="p-5">
                        @if (isset($item->category) && $item->category instanceof \BackedEnum)<p class="text-xs font-semibold uppercase text-amber-700">{{ $item->category->label() }}</p>@endif
                        @if (isset($item->type) && $item->type instanceof \BackedEnum)<p class="text-xs font-semibold uppercase text-amber-700">{{ $item->type->label() }}</p>@endif
                        <h2 class="mt-2 text-xl font-semibold">{{ $title }}</h2>
                        <p class="mt-3 text-sm text-slate-600">{{ $translation?->short_description }}</p>
                        <a href="{{ route("{$routePrefix}.show", [app()->getLocale(), $translation?->slug]) }}" class="mt-5 inline-block text-sm font-semibold text-amber-700">{{ __('cms.read_more') }}</a>
                    </div>
                </article>
            @empty
                <p class="text-slate-500">{{ __('cms.no_content') }}</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $items->links() }}</div>
    </section>
</x-layouts.public>

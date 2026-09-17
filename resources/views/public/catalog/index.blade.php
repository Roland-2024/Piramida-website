@php $plural = __('cms.'.explode('.', $routePrefix)[1]); @endphp
<x-layouts.public :title="$plural" :language-urls="$languageUrls">
    <section class="site-shell py-16">
        <h1 class="public-title">{{ $plural }}</h1>
        <div class="mt-9 grid gap-6 md:grid-cols-2">
            @forelse ($items as $item)
                @php
                    $translation = $item->translation(app()->getLocale(), false);
                    $title = data_get($translation, $translationTitleColumn);
                @endphp
                <article class="content-card">
                    @if (method_exists($item, 'featuredMedia') && $item->featuredMedia)
                        <img loading="lazy" src="{{ $item->featuredMedia->url() }}" alt="{{ app()->getLocale() === 'en' ? $item->featuredMedia->alt_text_en : $item->featuredMedia->alt_text_al }}" class="h-80 w-full rounded-xl object-cover">
                    @endif
                    <div class="p-5">
                        @if (isset($item->category) && $item->category instanceof \BackedEnum)<p class="text-xs font-semibold uppercase accent">{{ $item->category->label() }}</p>@endif
                        @if (isset($item->type) && $item->type instanceof \BackedEnum)<p class="text-xs font-semibold uppercase accent">{{ $item->type->label() }}</p>@endif
                        <h2 class="mt-2 text-xl font-semibold">{{ $title }}</h2>
                        <p class="mt-3 text-sm muted">{{ $translation?->short_description }}</p>
                        <a href="{{ route("{$routePrefix}.show", [app()->getLocale(), $translation?->slug]) }}" class="mt-5 inline-block text-sm font-semibold accent">{{ __('cms.read_more') }}</a>
                    </div>
                </article>
            @empty
                <p class="muted">{{ __('cms.no_content') }}</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $items->links() }}</div>
    </section>
</x-layouts.public>

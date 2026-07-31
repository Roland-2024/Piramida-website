<x-layouts.public :title="$translation->seo_title ?: $translation->title" :description="$translation->seo_description ?: $translation->excerpt" :language-urls="$languageUrls">
    <article class="mx-auto max-w-4xl px-5 py-16 lg:px-8">
        <p class="text-sm font-semibold text-amber-700">{{ $article->published_at->format('d M Y') }}</p>
        <h1 class="mt-4 text-4xl font-semibold tracking-tight sm:text-5xl">{{ $translation->title }}</h1>
        <p class="mt-5 text-xl text-slate-600">{{ $translation->excerpt }}</p>
        @if ($article->featuredMedia)<img src="{{ $article->featuredMedia->url() }}" alt="" class="mt-9 max-h-[32rem] w-full rounded-3xl object-cover">@endif
        <div class="prose-content mt-10 text-slate-700">{!! $translation->content !!}</div>
        @if ($article->gallery->isNotEmpty())
            <div class="mt-10 grid gap-4 sm:grid-cols-2">
                @foreach ($article->gallery as $media)
                    <img src="{{ $media->url() }}" alt="{{ app()->getLocale() === 'en' ? $media->alt_text_en : $media->alt_text_al }}" class="h-72 w-full rounded-2xl object-cover">
                @endforeach
            </div>
        @endif
    </article>
</x-layouts.public>

<x-layouts.public :title="$translation->seo_title ?: $translation->title" :description="$translation->seo_description ?: $translation->short_description" :language-urls="$languageUrls">
    <section class="border-b border-slate-200 bg-white"><div class="mx-auto max-w-5xl px-5 py-16 text-center lg:px-8"><h1 class="text-4xl font-semibold tracking-tight sm:text-5xl">{{ $translation->title }}</h1><p class="mx-auto mt-5 max-w-2xl text-lg text-slate-600">{{ $translation->short_description }}</p></div></section>
    <div class="mx-auto max-w-5xl px-5 py-10 lg:px-8">
        @if ($translation->content)<article class="prose-content text-slate-700">{!! $translation->content !!}</article>@endif
        <div class="mt-10 divide-y divide-slate-200">@foreach ($page->sections as $section) @include('public.partials.section', ['section' => $section]) @endforeach</div>
    </div>
</x-layouts.public>

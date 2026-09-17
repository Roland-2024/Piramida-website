<x-layouts.public :title="$translation->seo_title ?: $translation->title" :description="$translation->seo_description ?: $translation->short_description" :language-urls="$languageUrls">
    <div class="site-shell">
        <header class="page-heading"><h1>{{ $translation->title }}</h1>@if($translation->short_description)<p>{{ $translation->short_description }}</p>@endif</header>
        @if ($translation->content)<article class="prose-content mx-auto max-w-4xl">{!! $translation->content !!}</article>@endif
        @foreach ($page->sections as $section) @include('public.partials.section', ['section' => $section]) @endforeach
    </div>
</x-layouts.public>

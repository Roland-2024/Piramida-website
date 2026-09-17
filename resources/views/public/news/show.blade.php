<x-layouts.public :title="$translation->seo_title ?: $translation->title" :description="$translation->seo_description ?: $translation->excerpt" :language-urls="$languageUrls" :styles="['homepage']">
<div class="mx-auto pt-[100px] pb-24 relative z-0">
        <div class="page-top-bg hidden sm:block" aria-hidden="true"></div>

        <!-- Hero -->
        <div class="text-center">
            <span class="title-50 px-5 py-2 text-white">
                {{ $translation->title }}
            </span>

            <div class="title_16 text-[#CBFF00] pt-[16px]">
                {{ $article->published_at->format('M d, Y') }}
            </div>
        </div>

        <div class="max-w-[1600px] w-full mx-auto px-6 mt-[40px]">
            @if($article->featuredMedia)<img src="{{ $article->featuredMedia->url() }}" alt="{{ $translation->title }}"
                class="max-h-[680px] rounded-[20px] w-full object-cover">@endif
        </div>


        <article class="mx-auto max-w-6xl px-6 md:py-16 py-8 space-y-6 title_18 prose-content">
{!! $translation->content !!}
@if($article->gallery->isNotEmpty())<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2">
@foreach($article->gallery as $media)<img src="{{ $media->url() }}" alt="{{ app()->getLocale() === 'en' ? $media->alt_text_en : $media->alt_text_al }}" class="w-full h-[260px] object-cover rounded-[16px]" loading="lazy">@endforeach
</div>@endif</article>

        <img src="/template/images/greeen.png" alt="" aria-hidden="true"
            class="pointer-events-none absolute right-[95px] -z-10 hidden w-[55%] max-w-[57rem] sm:block"
            style="top: 1530px;" />

        <section class="pt-16 pb-[120px] px-6">

            <!-- News heading -->
            <div class="title-60 text-center uppercase tracking-wide mb-10">
                {{ __('cms.related_posts') }}
            </div>

            <!-- News cards -->
            <div id="newsScroll" class="news-scroll max-w-7xl mx-auto flex sm:grid overflow-x-auto sm:overflow-visible
            snap-x snap-mandatory sm:snap-none
            gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 -mx-6 px-6 sm:mx-0 sm:px-0 md:mx-auto">

                @foreach($relatedNews as $newsItem) @include('public.news._card') @endforeach</div><!-- Dots (mobile only) -->

        </section>
        <!-- Fade to black, transitioning into footer -->
        <div
            class="md:block hidden h-[200px] w-full bg-gradient-to-b from-transparent to-black -mb-24 mt-[-1px] pointer-events-none">
        </div>
    </div>


</x-layouts.public>

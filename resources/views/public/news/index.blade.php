<x-layouts.public :title="__('cms.news')" :language-urls="$languageUrls" :styles="['homepage']">
<h1 class="sr-only">{{ __('cms.news') }}</h1>
@if($articles->isNotEmpty())<div class="mx-auto pt-[100px] pb-24 relative z-0">
        <div class="page-top-bg hidden sm:block" aria-hidden="true"></div>

        <!-- Hero -->
        <div class="max-w-[1600px] w-full mx-auto px-6 mt-[40px]">
            <div class="template-news-hero relative rounded-[20px] overflow-hidden">
                @if($articles->first()->featuredMedia)<img src="{{ $articles->first()->featuredMedia->url() }}" alt="{{ $articles->first()->translation(app()->getLocale(), false)->title }}" class="max-h-[680px] w-full object-cover">@endif

                <!-- Gradient overlay, left side only -->
                <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/30 to-transparent"></div>

                <!-- Text content -->
                <div class="template-news-hero-copy absolute bottom-0 left-0 w-full text-center pb-[48px] px-6">
                    <div class="title_16 text-[#CBFF00]">
                        {{ $articles->first()?->published_at->format('M d, Y') }}
                    </div>

                    <span class="title-50 px-5 py-2 text-white block">
                        <a href="{{ route('public.news.show', [app()->getLocale(), $articles->first()->translation(app()->getLocale(), false)->slug]) }}">{{ $articles->first()->translation(app()->getLocale(), false)->title }}</a>
                    </span>
                </div>
            </div>
        </div>

        <!-- News Section -->
        <section class="max-w-6xl w-full mx-auto px-6 mt-[60px]">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @foreach ($articles as $newsItem) @include('public.news._card') @endforeach
</div><div class="template-pagination">{{ $articles->links() }}</div></section>

        <!-- Fade to black, transitioning into footer -->
        <div class="md:block hidden h-[200px] w-full bg-gradient-to-b from-transparent to-black -mb-24 mt-[-1px] pointer-events-none"></div>
    </div>

    @else<p class="template-empty">{{ __('cms.no_content') }}</p>@endif
</x-layouts.public>

<x-layouts.public :title="$translation->seo_title ?: $translation->title" :description="$translation->seo_description ?: $translation->short_description" :language-urls="$languageUrls" :styles="['education', 'education-carousel-enhanced']">
    <div class="education-main">
        <div class="flex justify-center px-6 pt-12 md:pt-72">
            <h1 class="max-md:mt-14 font-anton uppercase text-[38px] leading-none text-[#CBFF00] md:text-[50px] lg:text-[60px]">{{ $translation->title }}</h1>
        </div>
        <section class="relative mt-10 overflow-hidden pb-1 md:-mt-24">
            <div class="slider-outer" id="sliderOuter">
                <div class="slider-viewport" id="viewport" tabindex="0" role="region" aria-label="{{ $translation->title }}">
                    <div class="slider-track" id="track">
                        @foreach ($slides as $slide)
                            <article class="card">
                                <img src="{{ $slide['url'] }}" alt="{{ $slide['title'] }}" draggable="false">
                                <div class="sr-only">
                                    <h2 data-slide-title>{{ $slide['title'] }}</h2>
                                    <p data-slide-description>{{ $slide['description'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="mt-2 max-md:mt-4 flex items-center justify-center px-6 text-center md:mt-8">
                <div class="max-w-4xl">
                    <h2 id="activeSlideTitle" class="text-[24px] font-normal text-[#CBFF00] md:text-[20px] lg:text-[24px]">{{ $slides->first()['title'] ?? $translation->title }}</h2>
                    <p id="activeSlideDescription" class="text-[14px] md:pb-12 font-normal leading-relaxed text-white/75 md:text-[16px]">{{ $slides->first()['description'] ?? strip_tags($translation->content ?? '') }}</p>
                </div>
            </div>
        </section>
        <div id="carouselDots" class="flex items-center justify-center gap-3 pb-12 pt-2 md:hidden" aria-label="{{ __('cms.navigation') }}"></div>
    </div>
    <noscript><style>#track {display:flex;overflow-x:auto;gap:14px}.card {position:relative;flex:0 0 280px;height:360px}</style></noscript>
</x-layouts.public>

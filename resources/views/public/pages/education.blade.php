@php
$presentation = $page->sections->firstWhere('type', \App\Enums\SectionType::TextImage);
$slides = $presentation?->gallery->map(fn ($media) => ['label' => (app()->getLocale() === 'en' ? $media->alt_text_en : $media->alt_text_al) ?: $translation->title, 'img' => $media->url()])->values()->all() ?? [];
@endphp
<x-layouts.public :title="$translation->seo_title ?: $translation->title" :description="$translation->seo_description ?: $translation->short_description" :language-urls="$languageUrls" :styles="['education']">
<div class="education-main">
      <div>
        <div class="flex justify-center px-6 pt-12 md:pt-24">
          <h1
            class="font-anton uppercase text-[38px] leading-none text-[#CBFF00] md:text-[50px] lg:text-[60px]"
          >
            {{ $translation->title }}
          </h1>
        </div>

        <section class="relative mt-10 overflow-hidden pb-1 md:mt-1">
          <div class="slider-outer" id="sliderOuter">
            <div class="slider-viewport" id="viewport" tabindex="0" role="region" aria-label="{{ $translation->title }}">
              <div class="slider-track" id="track"></div>
            </div>
          </div>
        </section>

        <div class="mt-2 flex items-center justify-center px-6 text-center">
          <div class="max-w-4xl">
            <h2
              id="activeSlideTitle"
              class="text-[24px] font-normal text-[#CBFF00] md:text-[20px] lg:text-[24px]"
            >
              {{ $presentation?->translation()?->subtitle }}
            </h2>

            <p
              class="pb-6 text-[14px] font-normal leading-relaxed text-white/75 md:text-[16px]"
            >
              {{ strip_tags($presentation?->translation()?->description ?? $translation->content ?? '') }}
            </p>
          </div>
        </div>

        <div
          id="carouselDots"
          class="flex items-center justify-center gap-3 pb-12 pt-2 md:hidden"
          aria-label="Carousel navigation"
        ></div>
      </div>
    </div>
<script type="application/json" id="education-slides">{!! json_encode($slides, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
<noscript><div class="carousel-track">@foreach($presentation?->gallery ?? [] as $image)<img src="{{ $image->url() }}" alt="{{ $translation->title }}">@endforeach</div></noscript>
</x-layouts.public>

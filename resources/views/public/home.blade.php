@php
    $translation = $page?->translation(app()->getLocale());
    $pillars = $page?->sections->firstWhere('type', \App\Enums\SectionType::Features);
    $pillarItems = data_get($pillars?->structured_data, app()->getLocale().'.items', []);
    $videoSection = $page?->sections->first(fn ($section) => filled($section->video_url));
@endphp
<x-layouts.public :title="$translation?->seo_title ?: $translation?->title" :description="$translation?->seo_description ?: $translation?->short_description" :language-urls="$languageUrls" :styles="['homepage']" body-class="">
    <div class="intro" data-intro hidden>
        <div class="intro-content" aria-hidden="true">
            <img src="{{ asset('template/images/logo piramida.svg') }}" alt="" class="mx-auto mb-6 w-[97px]">
            <p>PIRAMIDA</p>
            <h2>{{ __('cms.space_to') }}<br><span class="intro-word">{{ __('cms.learn') }}</span> <span class="intro-word">{{ __('cms.build') }}</span> <span class="intro-word">{{ __('cms.connect') }}</span></h2>
            <p class="mx-auto mt-5 max-w-md text-white/60">{{ $translation?->short_description }}</p>
        </div>
        <div class="intro-stripes" aria-hidden="true"></div>
        <button class="skip-intro outline-button" type="button">{{ __('cms.skip_intro') }} ↓</button>
    </div>
    <div class="intro-swipe" data-intro-swipe aria-hidden="true" hidden></div>

<span class="sr-only">{{ $translation?->title }}</span>
    <section class="relative w-full h-[110vh] overflow-hidden">
      <!-- Background image -->
      <img src="/template/images/Piramida (2).png" alt="Piramida of Tirana"
        class="absolute inset-0 w-full h-full object-cover" />

      <!-- Bottom shadow overlay for text contrast -->
      <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t from-black/50 via-black/30 to-transparent"></div>

      <!-- Seamless fade into the next section's background color -->
      <div
        class="absolute inset-x-0 bottom-0 h-56 sm:h-72 md:h-50 bg-gradient-to-t from-[#0a1435] via-[#0a1435]/70 to-transparent">
      </div>

      <!-- Content -->
      <div class="relative z-10 flex flex-col items-center pt-[160px] px-4 text-center">
        <h1
          class="uppercase leading-[0.95] tracking-tight text-white drop-shadow-lg text-4xl sm:text-5xl md:text-6xl lg:text-[80px]">
          <span class="block">{{ __('cms.discover') }}</span>
          <span class="block">
            {{ __('cms.world_of') }} <span class="text-lime-400">Piramida</span>
          </span>
        </h1>
      </div>
    </section>
  <!-- ============= END HERO ============= -->

  <section class="relative bg-[#0a1435] pb-10 pt-10 sm:pt-16 md:pt-5 px-4 md:px-12">
    <div
      class="text-center text-white text-2xl sm:text-3xl md:text-4xl font-light leading-snug max-w-2xl mx-auto mb-10 sm:mb-14">
      {{ $pillars?->translation(app()->getLocale())?->title ?: __('cms.pillars_title') }}
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-7xl mx-auto">

      <!-- Card: Education -->
      <div
        id="education" class="group relative rounded-2xl overflow-hidden h-[280px] sm:h-[320px] md:h-[350px] bg-slate-900 border border-gray-800">
        <img src="/template/images/education.png" alt="Education spaces at Piramida"
          class="w-full h-full object-cover object-center" />

        <div class="absolute inset-0 z-10 flex flex-col h-full p-5">
          <img src="/template/images/Education.svg" class="w-[24px] h-[30px]" />

          <div class="mt-auto">
            <h3 class="text-lime-400 font-bold uppercase text-3xl mb-2">{{ data_get($pillarItems, '0.title', __('cms.education')) }}</h3>
            <p class="text-white/80 text-sm leading-snug max-w-[85%] sm:max-w-[70%]">{{ data_get($pillarItems, '0.text', '') }}</p>
          </div>
        </div>
      </div>

      <!-- Card: Innovation -->
      <div
        id="innovation" class="group relative rounded-2xl overflow-hidden h-[280px] sm:h-[320px] md:h-[350px] bg-slate-900 border border-gray-800">
        <img src="/template/images/innovation.png" alt="Innovation spaces at Piramida"
          class="w-full h-full object-cover object-center" />

        <div class="absolute inset-0 z-10 flex flex-col h-full p-5">
          <img src="/template/images/Innovation.svg" class="w-[24px] h-[30px]" />

          <div class="mt-auto">
            <h3 class="text-lime-400 font-bold uppercase text-3xl mb-2">{{ data_get($pillarItems, '1.title', __('cms.innovation')) }}</h3>
            <p class="text-white/80 text-sm leading-snug max-w-[85%] sm:max-w-[70%]">{{ data_get($pillarItems, '1.text', '') }}</p>
          </div>
        </div>
      </div>

      <!-- Card: Business -->
      <div
        id="business" class="group relative rounded-2xl overflow-hidden h-[280px] sm:h-[320px] md:h-[350px] bg-slate-900 border border-gray-800">
        <img src="/template/images/innovation.png" alt="Innovation spaces at Piramida"
          class="w-full h-full object-cover object-center" />

        <div class="absolute inset-0 z-10 flex flex-col h-full p-5">
          <img src="/template/images/Business.svg" class="w-[24px] h-[30px]" />



          <div class="mt-auto">
            <h3 class="text-lime-400 font-bold uppercase text-3xl mb-2">{{ data_get($pillarItems, '2.title', __('cms.business')) }}</h3>
            <p class="text-white/80 text-sm leading-snug max-w-[85%] sm:max-w-[70%]">{{ data_get($pillarItems, '2.text', '') }}</p>
          </div>
        </div>
      </div>

      <!-- Card: Art & Culture -->
      <div
        id="art" class="group relative rounded-2xl overflow-hidden h-[280px] sm:h-[320px] md:h-[350px] bg-slate-900 border border-gray-800">
        <img src="/template/images/innovation.png" alt="Innovation spaces at Piramida"
          class="w-full h-full object-cover object-center" />

        <div class="absolute inset-0 z-10 flex flex-col h-full p-5">
          <img src="/template/images/Art.svg" class="w-[24px] h-[30px]" />
          <div class="mt-auto">
            <h3 class="text-lime-400 font-bold uppercase text-3xl mb-2">{{ data_get($pillarItems, '3.title', __('cms.art')) }}</h3>
            <p class="text-white/80 text-sm leading-snug max-w-[85%] sm:max-w-[70%]">{{ data_get($pillarItems, '3.text', '') }}</p>
          </div>
        </div>
      </div>

    </div>
  </section>

  <section class="bg-[#0a1435] py-10 sm:pt-16 md:py-[150px] px-4 md:px-12 relative z-[8]">
    <img src="/template/images/green-gradient.svg" alt="Green gradient" class=" absolute right-0 top-[25%]" />

    <svg width="0" height="0" class="absolute">
      <defs>
        <clipPath id="piramidaClip" clipPathUnits="objectBoundingBox">
          <path d="M0.945313,0.132352
                       C0.945313,0.148595 0.952305,0.161763 0.960938,0.161763
                       H0.984375
                       C0.993008,0.161763 1,0.174916 1,0.191175
                       V0.897059
                       C1,0.913303 0.993008,0.926471 0.984375,0.926471
                       H0.617188
                       C0.608558,0.926471 0.601563,0.939638 0.601563,0.955882
                       V0.970588
                       C0.601563,0.986832 0.594567,1 0.585938,1
                       H0.015625
                       C0.006996,1 0,0.986832 0,0.970588
                       V0.867647
                       C0,0.851403 0.006996,0.838235 0.015625,0.838235
                       H0.023438
                       C0.032067,0.838235 0.039063,0.825068 0.039063,0.808824
                       V0.705882
                       C0.039063,0.689638 0.032067,0.676471 0.023438,0.676471
                       H0.015625
                       C0.006996,0.676471 0,0.663303 0,0.647059
                       V0.029412
                       C0,0.013168 0.006996,0 0.015625,0
                       H0.929688
                       C0.938320,0 0.945313,0.013168 0.945313,0.029412
                       V0.132352
                       Z" />
        </clipPath>
        <!-- Same shape as a mask instead of a clip-path. clip-path on an
                   element breaks backdrop-filter sampling on anything behind it
                   (a known Chrome/Safari compositing bug) — mask-image achieves
                   the identical visual shape without that side effect, which is
                   what was causing the ring area to render solid black. -->
        <mask id="piramidaMask" maskContentUnits="objectBoundingBox">
          <path fill="#ffffff" d="M0.945313,0.132352
                       C0.945313,0.148595 0.952305,0.161763 0.960938,0.161763
                       H0.984375
                       C0.993008,0.161763 1,0.174916 1,0.191175
                       V0.897059
                       C1,0.913303 0.993008,0.926471 0.984375,0.926471
                       H0.617188
                       C0.608558,0.926471 0.601563,0.939638 0.601563,0.955882
                       V0.970588
                       C0.601563,0.986832 0.594567,1 0.585938,1
                       H0.015625
                       C0.006996,1 0,0.986832 0,0.970588
                       V0.867647
                       C0,0.851403 0.006996,0.838235 0.015625,0.838235
                       H0.023438
                       C0.032067,0.838235 0.039063,0.825068 0.039063,0.808824
                       V0.705882
                       C0.039063,0.689638 0.032067,0.676471 0.023438,0.676471
                       H0.015625
                       C0.006996,0.676471 0,0.663303 0,0.647059
                       V0.029412
                       C0,0.013168 0.006996,0 0.015625,0
                       H0.929688
                       C0.938320,0 0.945313,0.013168 0.945313,0.029412
                       V0.132352
                       Z" />
        </mask>
      </defs>
    </svg>

    <!-- Video using the shape, with the original image as its thumbnail (poster) -->
    <div class="max-w-7xl mx-auto relative">
      <div class="video-clip">
        @if ($videoSection?->video_url && preg_match('/\\.(mp4|webm|ogg)(\\?.*)?$/i', $videoSection->video_url))
        <video id="piramidaVideo" poster="/template/images/Image Container.png" class="w-full aspect-[1280/680] object-cover" preload="metadata" playsinline controls><source src="{{ $videoSection->video_url }}"></video>
        @else
        <img src="/template/images/Image Container.png" alt="Piramida" class="w-full aspect-[1280/680] object-cover" loading="lazy">
        @endif
      </div>

      <!-- Custom play badge: blurred glass backing + rotating "ABOUT US" ring + solid center -->
      <a href="{{ $videoSection?->video_url ?: $aboutUrl }}" id="playBadge" aria-label="{{ __('cms.about_us') }}" class="play-badge">
        <span class="play-badge-blur" aria-hidden="true"></span>
        <svg viewBox="0 0 220 220" class="play-badge-ring" aria-hidden="true">
          <defs>
            <path id="badgeTextPath" d="M110,20 a90,90 0 1,1 -0.1,0" />
          </defs>
          <text class="play-badge-text">
            <textPath href="#badgeTextPath" startOffset="0%">ABOUT US &#8212; ABOUT US &#8212; ABOUT US
              &#8212; ABOUT US &#8212;</textPath>
          </text>
        </svg>
        <span class="play-badge-center">
          <svg viewBox="0 0 24 24" class="play-badge-icon" aria-hidden="true">
            <polygon points="9,6 19,12 9,18" />
          </svg>
        </span>
      </a>
    </div>

  </section>


  <div class="relative overflow-hidden bg-[#081434] ">

    <section class="relative z-10 py-16 sm:py-24">
      <div class="title-60 text-center">{{ __('cms.events') }}</div>
      <div class="text-center title-18 leading-snug max-w-2xl mx-auto  mb-10 sm:mb-14 px-4">
        {{ __('cms.events_intro') }}
      </div>

      <!-- Carousel wrapper -->
      <div class="relative mx-auto px-6 sm:px-10 md:px-10 max-w-[1670px]">

        <!-- Left arrow -->
        <button id="prevBtn" aria-label="Previous" type="button"
          class="hidden md:flex absolute -left-2 md:-left-5 top-1/2 -translate-y-1/2 z-20 h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur hover:bg-white/20 transition">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M15 18l-6-6 6-6" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>

        <!-- Right arrow -->
        <button id="nextBtn" aria-label="Next" type="button"
          class="hidden md:flex absolute -right-2 md:-right-5 top-1/2 -translate-y-1/2 z-20 h-10 w-10 items-center justify-center rounded-full bg-lime-400 text-black hover:bg-lime-300 transition">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 6l6 6-6 6" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </button>

        <!-- Cards -->
        <div id="cardTrack" class="card-scroll overflow-x-auto remove-scrollbar pb-2">

@forelse ($upcomingEvents as $event)
    @php $eventTranslation = $event->translation(app()->getLocale(), false); @endphp
    <div class="card-snap overflow-hidden {{ $loop->index === 1 || $upcomingEvents->count() === 1 ? 'is-active' : '' }}">
        <a href="{{ route('public.events.show', [app()->getLocale(), $eventTranslation->slug]) }}">
            @if ($event->featuredMedia)<img src="{{ $event->featuredMedia->url() }}" alt="{{ $eventTranslation->title }}" loading="lazy" class="w-full h-[450px] rounded-[12px] object-cover object-center">@endif
        </a>
        <div class="card-text pt-[40px] sm:p-5 mx-auto">
            <p class="title-green-20 mx-auto text-center">{{ $eventTranslation->title }}</p>
            <p class="title-16 fw-[500] mx-auto text-center w-[70%]">{{ $eventTranslation->short_description }}</p>
        </div>
    </div>
@empty <p class="template-empty">{{ __('cms.no_content') }}</p> @endforelse
        </div>
      </div>
      <div class="flex items-center justify-center h-[58px] pt-[30px]">
        <a href="{{ route('public.events.index', app()->getLocale()) }}"
          class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-medium text-[#0F174A] shadow-sm transition hover:shadow-md">
          <span>{{ __('cms.explore_all') }}</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
            stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7" />
          </svg>
        </a>
      </div>
    </section>

    <!-- Glow (replaces the old rose-gradient.svg) -->
    <div class="pointer-events-none absolute inset-0 z-0">
      <img src="/template/images/bg-low.jpg" alt="Rose gradient" class="w-full h-full object-cover" />
    </div>

    <section class="relative z-10 py-16 sm:py-24 px-[30px] md:px-[80px] lg:px-[120px]">

      <!-- Heading -->
      <div class="title-60 text-center uppercase tracking-wide leading-[1.1] mb-10">
        {{ __('cms.attractions') }} <br> &amp; {{ __('cms.experiences') }}
      </div>

      <!-- Attraction cards -->
      <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 gap-6 mb-24">

        <div class="rounded-3xl overflow-hidden bg-[#D9F044] rounded-[20px] max-h-[645px]">
          <div class="overflow-hidden">
            <img src="/template/images/piramida_block.jpg" alt="Piramida staircase from above" loading="lazy"
              class="w-full h-full object-cover max-h-[400px] p-[10px] rounded-[20px]" />
          </div>
          <div class="py-[50px] px-[40px]">
            <div class=" uppercase title-36 text-black mb-2">{{ $featuredAttractions->first()?->translation(app()->getLocale(), false)?->title ?: __('cms.step_into_piramida') }}</div>
            <p class="title-16 fw-[400] mb-5 text-black">
              {{ $featuredAttractions->first()?->translation(app()->getLocale(), false)?->short_description }}
            </p>
            <a href="{{ route('public.attractions.index', app()->getLocale()) }}"
              class="inline-flex items-center gap-2 bg-white text-black title-14-bold px-[20px] py-[16px] rounded-full hover:bg-white/90 transition">
              {{ __('cms.step_into_piramida') }}
              <img src="/template/images/arrow right black.svg" alt="Arrow icon" class="w-3.5 h-3.5 mt-1" />
            </a>
          </div>
        </div>

        <div class="rounded-3xl overflow-hidden bg-[#BEE3FF] rounded-[20px] max-h-[645px]">
          <div class="overflow-hidden">
            <img src="/template/images/piramida_block_1.jpg" alt="Piramida staircase from above" loading="lazy"
              class="w-full h-full object-cover max-h-[400px] p-[10px] rounded-[20px]" />
          </div>
          <div class="py-[50px] px-[40px]">
            <div class=" uppercase title-36 text-black mb-2">{{ __('cms.social_spaces') }}</div>
            <p class="title-16 fw-[400] mb-5 text-black">
              {{ __('cms.businesses_intro') }}
            </p>
            <a href="{{ route('public.businesses.index', app()->getLocale()) }}"
              class="inline-flex items-center gap-2 bg-white text-black title-14-bold px-[20px] py-[16px] rounded-full hover:bg-white/90 transition">
              {{ __('cms.explore_corners') }}
              <img src="/template/images/arrow right black.svg" alt="Arrow icon" class="w-3.5 h-3.5 mt-1" />
            </a>
          </div>
        </div>

      </div>

    </section>

  </div>
  <!-- End Events + Attractions wrapper -->


  <section class="pt-16 pb-[120px] px-6 md:bg-gradient-to-b from-[#081434] to-[#000000] bg-[#081434]">

    <!-- News heading -->
    <div class="title-60 text-center uppercase tracking-wide mb-10">
      {{ __('cms.news') }}
    </div>

    <!-- News cards -->
    <div id="newsScroll" class="news-scroll max-w-6xl mx-auto flex sm:grid overflow-x-auto sm:overflow-visible
          snap-x snap-mandatory sm:snap-none
          gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 -mx-6 px-6 sm:mx-0 sm:px-0 md:mx-auto">

@forelse ($latestNews as $article)
@php $newsTranslation = $article->translation(app()->getLocale(), false); @endphp
<!-- News 1 -->
      <article class="news-card shrink-0 w-full sm:w-auto snap-center rounded-[16px] border border-white/15 p-[30px]">
        <a href="{{ route('public.news.show', [app()->getLocale(), $newsTranslation->slug]) }}">
          <div class="rounded-xl">
            <img src="{{ $article->featuredMedia?->url() ?: asset('template/images/piramida_block_1.jpg') }}" alt="{{ $newsTranslation->title }}" loading="lazy"
              class="w-full sm:w-[350px] object-cover h-[380px] rounded-[12px]" />
          </div>
          <div class="pt-4 px-1">
            <div class="text-white title-22 mb-2">
              {{ $newsTranslation->title }}
            </div>
            <p class="text-white/50 title-14-semibold pt-2">{{ $article->published_at->format('M d, Y') }}</p>
          </div>
        </a>
      </article>
@empty <p class="template-empty">{{ __('cms.no_content') }}</p> @endforelse
    </div>
<div id="newsDots" class="flex sm:hidden justify-center gap-2 mt-6">@foreach ($latestNews as $article)<button class="news-dot w-2 h-2 rounded-full bg-white/30 transition-all" data-index="{{ $loop->index }}" aria-label="{{ __('cms.next') }} {{ $loop->iteration }}"></button>@endforeach</div>
  </section>
</x-layouts.public>

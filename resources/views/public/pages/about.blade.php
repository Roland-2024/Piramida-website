@php
    $overview = $page->sections->firstWhere('internal_name', 'About - Overview');
    $mission = $page->sections->firstWhere('internal_name', 'About - Mission');
    $history = $page->sections->firstWhere('internal_name', 'About - History');
    $timeline = $page->sections->firstWhere('internal_name', 'About - Timeline');
    $missionItems = data_get($mission?->structured_data, app()->getLocale().'.items', data_get($mission?->structured_data, 'al.items', []));
    $timelineItems = data_get($timeline?->structured_data, app()->getLocale().'.items', data_get($timeline?->structured_data, 'al.items', []));
@endphp
<x-layouts.public :title="$translation->seo_title ?: $translation->title" :description="$translation->seo_description ?: $translation->short_description" :language-urls="$languageUrls" :styles="['about']">
<div class="relative"><div class="page-top-bg hidden sm:block" aria-hidden="true"></div>
<div>
            <div class="mx-auto max-w-7xl px-6 pt-32 pb-24 sm:px-10 sm:pt-40">

                <!-- Hero heading -->
            <div class="text-center">
                <h1 class="title-60 uppercase">
                    {{ \Illuminate\Support\Str::beforeLast($translation->title, ' ') }} <span class="text-[#CBFF00]">{{ \Illuminate\Support\Str::afterLast($translation->title, ' ') }}</span>
                </h1>
            </div>

            <!-- Image + mission content -->
            <div class="mt-16 grid items-start gap-12 lg:mt-24 lg:grid-cols-2 lg:gap-16">

                <!-- Image with green gradient wedge peeking out bottom-left -->
                <div class="relative mx-auto h-[480px] w-full max-w-xl sm:h-[560px] lg:max-w-none">
                    <div class="absolute inset-0" aria-hidden="true"
                        style="clip-path: polygon(8% 0%, 100% 88%, 0% 100%); background: linear-gradient(155deg, #14532d 0%, #4ade80 55%, #cbff00 100%);">
                    </div>
                    <img src="{{ $overview?->primaryMedia?->url() ?? $page->featuredMedia?->url() ?? asset('template/images/About/about_piramida.jpg') }}"
                        alt="The Pyramid of Tirana plaza" class="absolute right-0 top-0 object-cover"
                        style="width: 92%; height: 88%;" />
                </div>

                <!-- Text content -->
                <div>
                    <span class="title-18-400 text-[#CBFF00]">
                        {{ $overview?->translation()?->subtitle }}
                    </span>

                    <div class="mt-4 title_48-400 text-white">
                        {{ $overview?->translation()?->title }}
                    </div>

                    <div class="mt-6 title-18 text-white prose-content">{!! $overview?->translation()?->description !!}</div>

                    <ul class="mt-8 space-y-4 title-18 text-white">@foreach ($missionItems as $item)<li class="flex items-start gap-3"><span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full bg-[#CBFF00]"></span><span>{{ data_get($item, 'text') ?: data_get($item, 'title') }}</span></li>@endforeach</ul>
                </div>
            </div>
        </div>
        </div>

        <!-- ===== History section ===== -->
        <section class="relative">
            <!-- Green gradient glow bridging History and Timeline, sits behind all content -->
            <img src="/template/images/green-gradient.svg" alt="" aria-hidden="true"
                class="pointer-events-none absolute -bottom-[28rem] right-0 -z-10 hidden w-[55%] max-w-[57rem] sm:block" />

            <div class="mx-auto max-w-7xl px-6 pb-24 sm:px-10">

                <!-- divider from the mission section above -->
                <div class="mb-20 h-px w-full bg-white/10 sm:mb-28"></div>

            <div class="grid gap-16 lg:grid-cols-2 lg:gap-16">

                <!-- Heading -->
                <div>
                    <div class="relative inline-block">
                        <span class="absolute -left-2 -top-9 select-none text-6xl font-extrabold uppercase tracking-tight text-transparent sm:-top-11 sm:text-7xl"
                            style="-webkit-text-stroke: 2px #24314f;" aria-hidden="true">
                            {{ $history?->translation()?->subtitle }}
                        </span>
                        <span class="relative text-6xl font-extrabold uppercase tracking-tight text-[#CBFF00] sm:text-7xl">
                            {{ $history?->translation()?->subtitle }}
                        </span>
                    </div>
                </div>

                <!-- Paragraph text -->
                <div class="title-16-400 text-white prose-content">{!! $history?->translation()?->description !!}</div>
            </div>

            <!-- Image with play button + arrow doodle bridging text and image -->
            <div class="relative mt-16 sm:mt-[160px]">
                <img src="/template/images/About/Dooodle.svg" alt="arrow-icon" aria-hidden="true"
                    class="pointer-events-none absolute -top-20 right-2 hidden h-auto w-[130%] max-w-none origin-bottom-right 2xl:block sm:-top-[12rem] sm:-right-[13.5rem]" />

                <div class="relative overflow-hidden rounded-2xl">
                    <img id="historyThumb"
                        src="{{ $history?->primaryMedia?->url() ?? asset('template/images/About/about_piramida.jpg') }}"
                        alt="Historic view of the Pyramid of Tirana"
                        class="h-[300px] w-full object-cover grayscale md:h-[680px]" />



                    <!-- decorative ascending squares + play button -->
                    <span class="absolute bottom-6 left-6 h-4 w-4 rounded-[3px] bg-[#CBFF00]" aria-hidden="true"></span>
                    <span class="absolute bottom-10 left-10 h-6 w-6 rounded-[3px] bg-[#CBFF00]" aria-hidden="true"></span>

                    @if($history?->video_url)<a href="{{ $history->video_url }}" target="_blank" rel="noopener" class="absolute bottom-16 left-16 public-button">{{ __('cms.play_video') }}</a>@endif
                </div>
            </div>
        </div>
        </section>

        <!-- ===== Timeline section ===== -->
        <section class="relative overflow-hidden">
            <!-- fade to black toward the bottom of the page (desktop only) -->
            <div class="pointer-events-none absolute inset-x-0 bottom-0 hidden h-[36rem] sm:block"
                style="background: linear-gradient(to bottom,
                    rgba(8,20,52,0) 0%,
                    rgba(8,20,52,0.12) 15%,
                    rgba(6,14,38,0.28) 30%,
                    rgba(4,9,26,0.45) 45%,
                    rgba(2,4,14,0.62) 58%,
                    rgba(0,0,0,0.78) 70%,
                    rgba(0,0,0,0.92) 85%,
                    #000000 100%);"
                aria-hidden="true"></div>

        <div class="relative mx-auto max-w-7xl px-6 md:py-24 py-4 sm:px-10">
            <div class="text-3xl font-normal text-white sm:text-4xl">{{ $timeline?->translation()?->title }}</div>

            <!-- Timeline cards: one set of markup — flex/snap carousel on mobile, grid on sm+ -->
            <div id="timelineCarousel"
                class="mt-8 flex gap-4 overflow-x-auto snap-x snap-mandatory scrollbar-hide sm:mt-12 sm:grid sm:grid-cols-3 sm:gap-10 sm:overflow-visible sm:snap-none">
                @foreach ($timelineItems as $item)
<div class="w-[88%] shrink-0 snap-start sm:w-auto sm:shrink">
<img src="{{ asset('template/images/About/'.(['Piramida_1980.png','Piramida_90.png','Piramida_2000.png'][$loop->index % 3])) }}" alt="" class="w-full rounded-2xl object-cover" loading="lazy">
<h3 class="mt-6 text-lg font-semibold text-[#CBFF00]">{{ data_get($item, 'title') }}</h3>
<p class="mt-3 text-sm leading-relaxed text-white/70">{{ data_get($item, 'text') }}</p>
</div>@endforeach
</div>
<!-- Dots: mobile only -->

        </div>
        </section>

</div></x-layouts.public>

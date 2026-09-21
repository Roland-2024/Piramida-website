<x-layouts.public :title="$translation->seo_title ?: $translation->title" :description="$translation->seo_description ?: $translation->short_description" :language-urls="$languageUrls" :styles="['single-event', 'popup']" body-class="" :footer="false">
<section class="event-section w-full min-h-screen flex items-center justify-center md:p-12 pt-28 md:pt-12">
        <div class="page-top-bg hidden sm:block" aria-hidden="true"></div>
        <!-- Gradient glow, same as About page -->
        <img src="/template/images/green-gradient.svg" alt="" aria-hidden="true"
            class="pointer-events-none absolute -bottom-[28rem] right-0 -z-10 hidden w-[55%] max-w-[57rem] sm:block" />

        <div class="w-full max-w-7xl grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-4 items-center px-4 md:px-0">

            <!-- LEFT: photo + coded green prism/pyramid fold -->
            <div class="prism-visual mx-auto">
                <!-- Coded gradient fold shape (was previously an <img> of an .svg export) -->
                <svg class="prism-shape" viewBox="0 0 768 653" preserveAspectRatio="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M768 497.398L0 653L99.3433 1L768 1.00006L768 497.398Z" fill="url(#prismGradient)" />
                    <path opacity="0.2" d="M411.5 0L0 652L99.3433 0L768 0L411.5 0Z" fill="black" />
                    <defs>
                        <radialGradient id="prismGradient" cx="0" cy="0" r="1"
                            gradientTransform="matrix(-1011.86 -836.855 837.39 -1011.22 794.628 740.276)"
                            gradientUnits="userSpaceOnUse">
                            <stop offset="0.137738" stop-color="#22C55E" />
                            <stop offset="0.463982" stop-color="#BEF264" />
                            <stop offset="0.849857" stop-color="#CBFF00" stop-opacity="0" />
                        </radialGradient>
                    </defs>
                </svg>

                @if($event->featuredMedia)<img src="{{ $event->featuredMedia->url() }}" alt="{{ $translation->title }}" class="prism-photo" />@endif
            </div>

            <!-- RIGHT: content -->
            <div class="w-full max-w-xl mx-auto md:mx-0 text-white px-2">
                <h1 class="leading-tight tracking-tight title_40">
                    {{ $translation->title }}
                </h1>

                <p class="mt-4 title_16">{{ $event->starts_at->format('d M Y · H:i') }} – {{ $event->ends_at->format('d M Y · H:i') }}</p>

                <div class="mt-6 text-sm leading-relaxed title_16 text-white prose-content">{!! $translation->description !!}</div>


                <div class="mt-8 border-t border-white/15"></div>

                <div class="mt-5 flex flex-wrap items-center gap-x-8 gap-y-2 text-sm text-white/80">
                    <div class="flex items-center gap-2">
                        <img src="/template/images/Location.svg" />
                        <span>{{ __('cms.location') }}: {{ $translation->location }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <img src="/template/images/Duration.svg" />
                        <span>{{ __('cms.duration') }}: {{ (int) $event->starts_at->diffInMinutes($event->ends_at) }} {{ __('cms.minutes') }}</span>
                    </div>
                </div>

                @if ($event->booking_mode->allowsInternal())<a href="#event-request" data-request-open="event-request"
                    class="mt-8 inline-flex items-center justify-center md:rounded-full px-7 py-3 text-sm font-bold text-[#1c2b1c] md:w-auto w-full rounded-[16px]"
                    style="background:#d7ef3f;">
                    {{ __('cms.join_us') }}
                </a>@endif
@if ($event->booking_mode->allowsExternal() && $event->external_url)<a href="{{ $event->external_url }}" target="_blank" rel="noopener" class="public-button mt-8">{{ __('cms.external_registration') }}</a>@endif
            </div>

        </div>
    </section>
@if($latestEvents->isNotEmpty())
<section class="max-w-7xl mx-auto py-14 px-5 md:px-0" data-carousel data-scroll-amount="300">
    <div class="flex items-center justify-center md:justify-between mb-7">
        <div class="flex items-center gap-3"><img src="/template/images/calendar-3d-icon.svg" alt="" class="hidden md:block"><h2 class="text-2xl md:text-3xl font-extrabold tracking-wide text-white md:text-lime-400 text-center md:text-left">{{ __('cms.latest_events') }}</h2></div>
        <div class="hidden md:flex gap-3"><button type="button" data-scroll="-1" class="nav-btn w-10 h-10 flex items-center justify-center" aria-label="{{ __('cms.previous') }}"><img src="/template/images/arrow right.svg" alt="" class="rotate-180"></button><button type="button" data-scroll="1" class="nav-btn w-10 h-10 flex items-center justify-center" aria-label="{{ __('cms.next') }}"><img src="/template/images/arrow right.svg" alt=""></button></div>
    </div>
    <div class="event-track carousel-track">
        @foreach($latestEvents as $latestEvent)
        @php $latestTranslation = $latestEvent->translation(app()->getLocale(), false); @endphp
        <a class="event-card" href="{{ route('public.events.show', [app()->getLocale(), $latestTranslation->slug]) }}">
            <div class="event-poster relative rounded-xl overflow-hidden">@if($latestEvent->featuredMedia)<img src="{{ $latestEvent->featuredMedia->url() }}" alt="{{ $latestTranslation->title }}" class="w-full h-full object-cover" loading="lazy">@endif</div>
            <div class="pt-3"><p class="title-24">{{ $latestTranslation->title }}</p><p class="text-xs text-white/50">{{ $latestEvent->starts_at->format('d M Y') }}</p></div>
        </a>
        @endforeach
    </div>
    <div data-carousel-dots class="flex md:hidden justify-center gap-2 mt-5" aria-label="{{ __('cms.navigation') }}"></div>
</section>
@endif
@if ($event->booking_mode->allowsInternal())
@include('public.events._request')
@endif
</x-layouts.public>

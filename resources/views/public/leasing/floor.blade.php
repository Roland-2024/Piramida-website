<x-layouts.public :title="$floorInfo[app()->getLocale()]" :language-urls="$languageUrls" :styles="['floor-plan']" body-class="event-background-page min-h-screen overflow-x-hidden bg-[#000929] text-white">
    <section class="floor-plan-section" aria-labelledby="floor-plan-title">
        <h1 id="floor-plan-title" class="sr-only">{{ $floorInfo[app()->getLocale()] }}</h1>
        <div class="floor-plan-card">
            <a href="{{ route('public.leasing.index', app()->getLocale()) }}" class="floor-plan-back" aria-label="{{ __('cms.back_to_map') }}">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 12H5M11 6L5 12L11 18" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <p class="floor-plan-level" aria-hidden="true">
                @if ($floorInfo['number'] !== '')
                    <span>{{ __('cms.floor') }}</span><span class="floor-plan-level-number">{{ $floorInfo['number'] }}</span>
                @else
                    <span>{{ $floorInfo[app()->getLocale()] }}</span>
                @endif
            </p>
            <div class="floor-plan-scroll"><div class="floor-plan-frame">
                @include('public.leasing.plans.'.$floor)
            </div></div>
        </div>
        <ul class="floor-plan-legend" aria-label="{{ __('cms.availability') }}">
            <li><span class="floor-plan-swatch is-available" aria-hidden="true"></span>{{ __('cms.unit_available') }}</li>
            <li><span class="floor-plan-swatch is-unavailable" aria-hidden="true"></span>{{ __('cms.unit_unavailable') }}</li>
        </ul>
    </section>
</x-layouts.public>

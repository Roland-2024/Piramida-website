<x-layouts.public :title="__('cms.careers')" :language-urls="$languageUrls" :styles="['careers']" :footer="false">
    <div class="mx-auto px-6 pt-[100px] pb-24 bg-[#081434] relative z-0">
        <div class="page-top-bg hidden sm:block" aria-hidden="true"></div>

        <!-- Hero -->
        <div class="text-center">
            <span class="hero-badge title-60 px-5 py-2 text-white">
                JOIN<span class="text-[#c6f135]">OUR</span>TEAM
            </span>

            <div class="title_48-400 text-white md:pt-[120px] pt-[60px]">
                Careers at Piramida
            </div>

            <p class="title-18 text-[#FFFFFF66]">
                Build, Create, and Grow With Us
            </p>

            <a href="#open-roles"
                class="mt-7 inline-flex items-center gap-2 bg-[#c6f135] text-[#05070f] title-14-bold p-[20px] rounded-[40px]">
                See Open Roles
                <span aria-hidden="true">→</span>
            </a>
        </div>

        <!-- Photo strip: one carousel that behaves as a mobile swipe-carousel and a desktop proportional row -->
        <div class="mt-12">
            <div id="photoCarousel"
                class="flex gap-3 overflow-x-auto snap-x snap-mandatory scrollbar-hide pl-6 sm:pl-0 sm:overflow-visible sm:snap-none">
                <div class="snap-start shrink-0 w-[85%] aspect-[4/5] rounded-2xl sm:w-auto sm:shrink sm:aspect-auto sm:h-72 sm:flex-[596] sm:rounded-xl overflow-hidden bg-[#0f1730]">
                    <img src="/template/images/Careers.jpg"
                        alt="Team on rooftop terrace" class="w-full h-full object-cover">
                </div>
                <div class="snap-start shrink-0 w-[85%] aspect-[4/5] rounded-2xl sm:w-auto sm:shrink sm:aspect-auto sm:h-72 sm:flex-[408] sm:rounded-xl overflow-hidden bg-[#0f1730]">
                    <img src="/template/images/Careers_1.jpg"
                        alt="Event crowd" class="w-full h-full object-cover">
                </div>
                <div class="snap-start shrink-0 w-[85%] aspect-[4/5] rounded-2xl sm:w-auto sm:shrink sm:aspect-auto sm:h-72 sm:flex-[494] sm:rounded-xl overflow-hidden bg-[#0f1730]">
                    <img src="/template/images/Careers_2.jpg"
                        alt="Store front" class="w-full h-full object-cover">
                </div>
                <div class="snap-start shrink-0 w-[85%] aspect-[4/5] rounded-2xl sm:w-auto sm:shrink sm:aspect-auto sm:h-72 sm:flex-[288] sm:rounded-xl overflow-hidden bg-[#0f1730]">
                    <img src="/template/images/Careers_3.jpg"
                        alt="Mall interior with crowd" class="w-full h-full object-cover">
                </div>
            </div>
            <div id="carouselDots" class="flex justify-center items-center gap-2 mt-4 sm:hidden">
                <span class="w-1.5 h-1.5 rounded-full bg-[#c6f135] transition-colors duration-200" data-dot></span>
                <span class="w-1.5 h-1.5 rounded-full bg-white/20 transition-colors duration-200" data-dot></span>
                <span class="w-1.5 h-1.5 rounded-full bg-white/20 transition-colors duration-200" data-dot></span>
                <span class="w-1.5 h-1.5 rounded-full bg-white/20 transition-colors duration-200" data-dot></span>
            </div>
        </div>


        <!-- Open roles -->
<span id="open-roles"></span>
        <div class="mt-16 text-center title-50 text-white">OPEN ROLES</div>
        <div class="mt-6 h-px bg-white/10 max-w-6xl mx-auto"></div>
        <div class="md:mt-[100px] mt-[50px] max-w-6xl mx-auto">

@forelse ($items as $item)
@php $translation = $item->translation(app()->getLocale(), false); @endphp
<details class="role-card rounded-2xl" @if($loop->index === 1) open @endif>
<summary class="flex items-center justify-between px-5 py-4"><span class="role-title title-26 py-3">{{ $translation->title }}</span><svg class="chev w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="none"><path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></summary>
<div class="panel"><div class="px-5 pb-5"><div class="role-body title-18 space-y-3 prose-content">{!! $translation->description !!}</div>
@if($translation->requirements)<div class="role-body title-18 prose-content mt-4">{!! $translation->requirements !!}</div>@endif
<div class="mt-5 flex items-center justify-between"><span class="title-16">{{ $translation->location }}</span><a href="{{ route('public.careers.show',[app()->getLocale(), $translation->slug]) }}" class="bg-[#c6f135] text-[#05070f] text-sm font-semibold px-5 py-2 rounded-full">{{ __('cms.apply') }}</a></div>
</div></div></details>
@empty <p class="template-empty">{{ __('cms.no_content') }}</p> @endforelse
<div class="template-pagination">{{ $items->links() }}</div>
        </div>



    </div>


</x-layouts.public>

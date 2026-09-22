<x-layouts.public :title="__('cms.attractions')" :language-urls="$languageUrls" :styles="['piramida-popup', 'attraction']" body-class="attractions-page">
@if($items->isNotEmpty())<section id="attractions" tabindex="0" class="relative overflow-hidden md:py-20 py-[5rem]">

        <div class="page-top-bg hidden sm:block" aria-hidden="true"></div>

        <div class="title-60 text-center text-[#CBFF00] uppercase p-[50px]">
            {{ __('cms.attractions') }}</div>

        <div class="attraction-visual">
            <div data-track>
                <button data-prev type="button" aria-label="Previous attraction">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <div data-cards>
                    @foreach($items as $attraction)
@php $entry = $attraction->translation(app()->getLocale(), false); @endphp
<figure class="attraction-card" data-index="{{ $loop->index }}">
@if($attraction->featuredMedia)<img src="{{ $attraction->featuredMedia->url() }}" alt="{{ $entry->title }}">@endif
<div class="attraction-content"><div class="tag">{{ $entry->location }}</div><div class="title">{{ $entry->title }}</div><div class="desc">{{ $entry->short_description }}</div></div>
</figure>@endforeach
                </div>

                <button data-next type="button" aria-label="Next attraction">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <div class="caption-overlay">
                <div class="tag" data-caption-tag></div>
                <div data-caption-title></div>
            </div>
        </div>

        <div class="caption">
            <div data-caption-desc></div>
        </div>

        <div class="attraction-dots" data-attraction-dots></div>
    </section>

    @else<p class="template-empty">{{ __('cms.no_content') }}</p>@endif
@if($items->hasPages())<div class="template-pagination">{{ $items->links() }}</div>@endif
@include('public.businesses._experiences', ['businesses' => $businesses])
</x-layouts.public>

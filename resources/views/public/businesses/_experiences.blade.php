<section id="experiences" class="relative overflow-hidden md:py-20 bg-gradient-to-b from-transparent to-black">

        <img src="/template/images/greeen.png" alt="" aria-hidden="true"
            class="pointer-events-none absolute -top-[45px] right-[230px] -z-10 hidden w-[55%] max-w-[57rem] sm:block" />

        <div class="title-60 text-center text-[#CBFF00] uppercase p-[50px] bg-transparent">
            {{ __('cms.experiences') }}</div>

        <div class="exp-track-wrap">
            <button data-exp-prev type="button" aria-label="Previous experience">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            <div data-exp-track>
                @foreach($businesses as $business)
@php $businessTranslation = $business->translation(app()->getLocale(), false); @endphp
<a class="exp-card" href="{{ route('public.businesses.show', [app()->getLocale(), $businessTranslation->slug]) }}" data-dialog-open="business-{{ $business->id }}" aria-haspopup="dialog">
<div class="exp-card-media">@if($business->featuredMedia)<img src="{{ $business->featuredMedia->url() }}" alt="{{ $businessTranslation->name }}" loading="lazy">@endif</div>
<div class="exp-card-meta"><span class="exp-avatar">@if($business->logoMedia)<img src="{{ $business->logoMedia->url() }}" alt="">@endif</span>
<span class="exp-meta-text"><span class="exp-label">{{ $businessTranslation->name }}</span><span class="exp-sublabel">{{ $business->category->label() }}</span></span></div>
</a>@endforeach
            </div>

            <button data-exp-next type="button" aria-label="Next experience">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        <div class="exp-dots" data-exp-dots></div>
         <!-- Fade to black, transitioning into footer -->
        <div class="md:block hidden h-[200px] w-full bg-gradient-to-b from-transparent to-black -mb-24 mt-[-1px] pointer-events-none"></div>
    </section>


@foreach($businesses as $business)
<dialog id="business-{{ $business->id }}" class="template-dialog" aria-label="{{ $business->translation(app()->getLocale(), false)->name }}">
@include('public.businesses._popup', ['business' => $business, 'dialog' => true])
</dialog>@endforeach

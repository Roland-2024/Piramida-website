<x-layouts.public :title="$translation->seo_title ?: $translation->title" :description="$translation->seo_description ?: $translation->short_description" :language-urls="$languageUrls" :styles="['event-spaces-popup','leasing-form']" body-class="desktop-page-background leasing-form-background" :footer="false">
@if($item->type === \App\Enums\SpaceType::Leasing)
<div class="event-spaces-main"><section class="leasing-form-hero" aria-labelledby="leasing-form-title">
          <div class="leasing-form-title-wrap">
            <div id="leasing-form-title" class="leasing-form-title">
              <span>PIRAMIDA</span><span>{{ __('cms.leasing') }}</span>
            </div>
            <p class="leasing-form-kicker">{{ __('cms.request_confirmation_notice') }}</p>
          </div>

          <div class="leasing-form-carousel">
            <button
              class="leasing-form-carousel-arrow leasing-form-carousel-arrow-left"
              type="button"
              aria-label="Previous leasing images"
            >
              <span aria-hidden="true"></span>
            </button>

            <div class="leasing-form-gallery">@foreach(collect([$item->featuredMedia])->filter()->merge($item->gallery)->unique('id') as $media)<img src="{{ $media->url() }}" alt="{{ $translation->title }}" loading="lazy">@endforeach</div>

            <button
              class="leasing-form-carousel-arrow leasing-form-carousel-arrow-right"
              type="button"
              aria-label="Next leasing images"
            >
              <span aria-hidden="true"></span>
            </button>
          </div>



          <div class="leasing-form-content">
            <div class="leasing-form-description prose-content">{!! $translation->description !!}</div>

            <div class="leasing-form-summary" aria-label="Leasing details">
              <div class="space-name">
                <span>{{ __('cms.title') }}</span>
                <strong>{{ $translation->title }}</strong>
              </div>
              <div class="position">
                <span>{{ __('cms.location') }}</span>
                <strong>{{ $translation->location }}</strong>
              </div>

              <div class="area">
                <span>{{ __('cms.area') }}</span>
                <strong>{{ $item->area_sqm }} m²</strong>
              </div>

            </div>

            @if($item->booking_mode->allowsInternal())
@include('public.submissions._feedback')
@include('public.submissions._leasing-form', ['action' => route('public.spaces.leasing-request', [app()->getLocale(), $translation->slug])])
@endif
          </div>
        </section>

        </div>
@elseif($item->booking_mode->allowsInternal())
@include('public.spaces._request')
@else
<div class="site-shell py-40"><h1 class="title-60">{{ $translation->title }}</h1><div class="prose-content mt-8">{!! $translation->description !!}</div></div>
@endif
@if($item->booking_mode->allowsExternal() && $item->external_url)<div class="text-center pb-12"><a href="{{ $item->external_url }}" target="_blank" rel="noopener" class="public-button">{{ __('cms.external_form') }}</a></div>@endif
</x-layouts.public>

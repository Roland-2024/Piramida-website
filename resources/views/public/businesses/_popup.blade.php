@php
$businessTranslation = $business->translation(app()->getLocale(), false);
$images = collect([$business->featuredMedia])->filter()->merge($business->gallery)->unique('id');
@endphp

        <section class="place-popup-section" aria-label="{{ $businessTranslation->name }}">
          <div class="place-popup-shape" aria-hidden="true"></div>

          @if($dialog ?? false)<button data-dialog-close="business-{{ $business->id }}" class="place-popup-close" type="button" aria-label="{{ __('cms.close') }}">@else<a class="place-popup-close" href="{{ route('public.businesses.index', app()->getLocale()) }}" aria-label="{{ __('cms.close') }}">@endif
            <img src="/template/images/Cross.svg" alt="" />
          @if($dialog ?? false)</button>@else</a>@endif

          <article

            class="place-popup-card"

          >
            <header class="place-popup-header">
              <div  class="place-popup-logo" aria-hidden="true">@if($business->logoMedia)<img src="{{ $business->logoMedia->url() }}" alt="">@endif</div>
              <div>
                <h1>{{ $businessTranslation->name }}</h1>
                <p>{{ $business->category->label() }}</p>
              </div>
            </header>

            <div class="place-popup-gallery" data-gallery>
              <div class="place-popup-gallery-frame">
              @foreach($images as $image)<img data-gallery-image @if(!$loop->first) hidden @endif src="{{ $image->url() }}" alt="{{ app()->getLocale() === 'en' ? $image->alt_text_en : $image->alt_text_al }}" loading="lazy">@endforeach
              </div>

              <div
                data-gallery-dots
                class="place-gallery-dots"
                aria-label="Gallery navigation"
              ></div>

              <button
                data-gallery-step="-1" class="place-gallery-button place-gallery-button-prev"
                type="button"
                aria-label="Previous image"
              >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M15 6L9 12L15 18" />
                </svg>
              </button>

              <button
                data-gallery-step="1" class="place-gallery-button place-gallery-button-next"
                type="button"
                aria-label="Next image"
              >
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M9 6L15 12L9 18" />
                </svg>
              </button>
            </div>

            <div class="place-popup-description prose-content">{!! $businessTranslation->description ?: e($businessTranslation->short_description) !!}</div>

            <p class="place-popup-location">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path
                  d="M12 21S6.5 16.2 6.5 10.8A5.5 5.5 0 0 1 12 5.3A5.5 5.5 0 0 1 17.5 10.8C17.5 16.2 12 21 12 21Z"
                />
                <circle cx="12" cy="10.8" r="1.7" />
              </svg>
              <span>{{ __('cms.location') }}: {{ $businessTranslation->address }}</span>
            </p>
          <div class="template-business-links">@if($businessTranslation->opening_hours)<p>{{ $businessTranslation->opening_hours }}</p>@endif
@if($business->website_url)<a href="{{ $business->website_url }}" target="_blank" rel="noopener">{{ __('cms.website') }} ↗</a>@endif
@if($business->phone)<a href="tel:{{ $business->phone }}">{{ $business->phone }}</a>@endif</div></article>
        </section>

<x-layouts.public :title="$spaceType === \App\Enums\SpaceType::Leasing ? __('cms.leasing') : __('cms.event_spaces')" :language-urls="$languageUrls" :styles="['event-spaces', 'event-spaces-popup']">
<div class="event-spaces-main">
        <section
          class="event-spaces-desktop"
          aria-labelledby="event-spaces-desktop-title"
        >
          <div class="event-spaces-desktop-copy">
            <div
              id="event-spaces-desktop-title"
              class="event-spaces-desktop-title"
            >
              <span>{{ $spaceType === \App\Enums\SpaceType::Leasing ? __('cms.leasing') : __('cms.events') }}</span>@if($spaceType !== \App\Enums\SpaceType::Leasing)<span>{{ __('cms.spaces') }}</span>@endif
            </div>

            <p class="event-spaces-desktop-description">
              {{ $spaceType === \App\Enums\SpaceType::Leasing ? __('cms.leasing_intro') : __('cms.event_spaces_intro') }}
            </p>
          </div>

          <div class="event-spaces-carousel">
            <button
              class="event-spaces-carousel-arrow event-spaces-carousel-arrow-left"
              type="button"
              aria-label="Previous event spaces"
            >
              <span aria-hidden="true"></span>
            </button>

            <div class="event-spaces-card-track">@forelse ($items as $space)
@php $translation = $space->translation(app()->getLocale(), false); @endphp
<a class="event-spaces-card" href="{{ route('public.spaces.show', [app()->getLocale(), $translation->slug]) }}" @if($space->booking_mode->allowsInternal()) data-dialog-open="space-request-{{ $space->id }}" @endif>
    <div class="event-spaces-card-media">@if($space->featuredMedia)<img src="{{ $space->featuredMedia->url() }}" alt="{{ $translation->title }}" loading="lazy">@endif<span class="event-spaces-card-button">{{ $spaceType === \App\Enums\SpaceType::Leasing ? __('cms.request_information') : __('cms.book_now') }}</span></div>
    <div class="event-spaces-card-title">{{ $translation->title }}</div>
    <div class="event-spaces-card-meta">{{ $translation->location }} @if($space->area_sqm) | {{ $space->area_sqm + 0 }} m² @endif @if($space->capacity) | {{ __('cms.capacity') }}: {{ $space->capacity }} @endif</div>
</a>
@empty <p class="template-empty">{{ __('cms.no_content') }}</p> @endforelse
            </div>

            <button
              class="event-spaces-carousel-arrow event-spaces-carousel-arrow-right"
              type="button"
              aria-label="Next event spaces"
            >
              <span aria-hidden="true"></span>
            </button>
          </div>

          <div class="event-spaces-carousel-pagination" aria-label="{{ __('cms.navigation') }}"></div>
        </section>

        <section class="event-spaces-hero" aria-labelledby="event-spaces-title">
          <div class="event-spaces-copy">
            <div id="event-spaces-title" class="event-spaces-title">
              <span>{{ $spaceType === \App\Enums\SpaceType::Leasing ? __('cms.leasing') : __('cms.events') }}</span>@if($spaceType !== \App\Enums\SpaceType::Leasing)<span>{{ __('cms.spaces') }}</span>@endif
            </div>

            <p class="event-spaces-description">
              {{ $spaceType === \App\Enums\SpaceType::Leasing ? __('cms.leasing_intro') : __('cms.event_spaces_intro') }}
            </p>

            <a
              class="event-spaces-scroll"
              href="#space-1"
              aria-label="Scroll to Space 1"
            >
              <span aria-hidden="true"></span>
            </a>
          </div>

          <div class="event-spaces-dots" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
          </div>
        </section>

@foreach ($items as $space)
@php $translation = $space->translation(app()->getLocale(), false); @endphp
<section id="space-{{ $loop->iteration }}" class="event-space-panel">
    @if($space->featuredMedia)<img class="event-space-panel-image" src="{{ $space->featuredMedia->url() }}" alt="" loading="lazy">@endif
    <div class="event-space-panel-content"><div class="event-space-panel-title">{{ $translation->title }}</div>
    <p class="event-space-panel-meta">{{ $translation->location }} @if($space->area_sqm) | {{ $space->area_sqm + 0 }} m² @endif @if($space->capacity) | {{ __('cms.capacity') }}: {{ $space->capacity }} @endif</p>
    <a class="event-space-panel-button" href="{{ route('public.spaces.show', [app()->getLocale(), $translation->slug]) }}" @if($space->booking_mode->allowsInternal()) data-dialog-open="space-request-{{ $space->id }}" @endif>{{ $spaceType === \App\Enums\SpaceType::Leasing ? __('cms.request_information') : __('cms.book_now') }} ↗</a></div>
</section>
@if($space->booking_mode->allowsInternal())
<dialog id="space-request-{{ $space->id }}" class="template-dialog" aria-label="{{ $translation->title }}" data-feedback="{{ ($errors->any() && (string) old('_space_id') === (string) $space->id) || (string) session('submitted_space_id') === (string) $space->id ? 'true' : 'false' }}">
    @include('public.spaces._request', ['item' => $space, 'dialog' => true])
</dialog>
@endif
@endforeach
<div class="template-pagination">{{ $items->links() }}</div>
</div>
</x-layouts.public>

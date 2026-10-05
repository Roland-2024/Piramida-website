@php
    $formValues = !($dialog ?? false) || (string) old('_space_id') === (string) $item->id ? old() : [];
@endphp
<section class="event-space-section">
    <div class="request-frame">
        <div class="event-space-shape" aria-hidden="true"></div>

        @if($dialog ?? false)
        <button type="button" class="event-space-close" data-dialog-close="space-request-{{ $item->id }}" aria-label="{{ __('cms.close') }}"><img src="/template/images/Cross.svg" alt=""></button>
        @else
        <a href="{{ route('public.spaces.index', app()->getLocale()) }}"
          class="event-space-close"
          type="button"
          aria-label="Close booking form"
          data-close-event-modal
        >
          <img
            src="/template/images/Cross.svg"
            alt=""
            class="h-7 w-7 object-contain"
          />
        </a>
        @endif

        <div class="event-space-card">
          @if(!($dialog ?? false))<a href="{{ route('public.spaces.index', app()->getLocale()) }}"
            class="event-space-close event-space-close-mobile"
            type="button"
            aria-label="Close booking form"
            data-close-event-modal
          >
            <span aria-hidden="true"></span>
          </a>@endif

          <h1 id="space-{{ $item->id }}-title" class="event-space-title">
            {{ $translation->title }}
          </h1>
          <p class="event-space-subtitle hidden md:block">
            {{ __('cms.request_confirmation_notice') }}
          </p>

          <form class="event-space-form" action="{{ route('public.spaces.event-request', [app()->getLocale(), $translation->slug]) }}" method="post">
            @csrf
<input type="hidden" name="_space_id" value="{{ $item->id }}">
@if(!($dialog ?? false) || (string) old('_space_id') === (string) $item->id || (string) session('submitted_space_id') === (string) $item->id)
@include('public.submissions._feedback')
@endif
<div class="event-space-row">
              <div class="event-space-field">
                <label class="sr-only" for="space-{{ $item->id }}-fullName">Full name</label>
                <input
                  id="space-{{ $item->id }}-fullName"
                  name="name" value="{{ data_get($formValues, 'name') }}"
                  type="text"
                  autocomplete="name"
                  placeholder="Full Name*"
                  required
                />
              </div>

              <div class="event-space-field">
                <label class="sr-only" for="space-{{ $item->id }}-email">Email Address</label>
                <input
                  id="space-{{ $item->id }}-email"
                  name="email" value="{{ data_get($formValues, 'email') }}"
                  type="email"
                  autocomplete="email"
                  placeholder="Email Address*"
                  required
                />
              </div>
            </div>

            <div class="event-space-row">
              <div class="event-space-field">
                <label class="sr-only" for="space-{{ $item->id }}-guests">Number of Guests</label>
                <input
                  id="space-{{ $item->id }}-guests"
                  name="attendees" value="{{ data_get($formValues, 'attendees') }}"
                  type="number"
                  min="1"
                  inputmode="numeric"
                  placeholder="Number of Guests*"
                  required
                />
              </div>

              <div class="event-space-field">
                <label class="sr-only" for="space-{{ $item->id }}-phone">Phone Number</label>
                <input
                  id="space-{{ $item->id }}-phone"
                  name="phone" value="{{ data_get($formValues, 'phone') }}"
                  type="tel"
                  autocomplete="tel"
                  inputmode="tel"
                  placeholder="Phone Number*"
                  required
                />
              </div>
            </div>

            <div class="event-space-field">
              <label class="sr-only" for="space-{{ $item->id }}-eventType">Event type</label>
              <div class="event-space-select-wrapper">
                <select id="space-{{ $item->id }}-eventType" name="event_type" required>
                  <option value="">Event type*</option>
                  <option value="conference" @selected(data_get($formValues, 'event_type') === 'conference')>Conference</option>
                  <option value="wedding" @selected(data_get($formValues, 'event_type') === 'wedding')>Wedding</option>
                  <option value="corporate" @selected(data_get($formValues, 'event_type') === 'corporate')>Corporate Event</option>
                  <option value="private" @selected(data_get($formValues, 'event_type') === 'private')>Private Event</option>
                  <option value="other" @selected(data_get($formValues, 'event_type') === 'other')>Other</option>
                </select>
                <span
                  class="event-space-select-arrow"
                  aria-hidden="true"
                ></span>
              </div>
            </div>

            <div class="event-space-row">
              <div class="event-space-field">
                <label class="sr-only" for="space-{{ $item->id }}-date">Preferred Date</label>
                <input
                  id="space-{{ $item->id }}-date"
                  name="preferred_date" value="{{ data_get($formValues, 'preferred_date') }}"
                  type="date"
                  placeholder="Preferred Date"
                  required
                />
              </div>

              <div class="event-space-field">
                <label class="sr-only" for="space-{{ $item->id }}-time">Preferred Time</label>
                <input
                  id="space-{{ $item->id }}-time"
                  name="preferred_time" value="{{ data_get($formValues, 'preferred_time') }}"
                  type="time"
                  placeholder="Preferred Time"
                  required
                />
              </div>
            </div>

            <div class="event-space-field">
              <label class="sr-only" for="space-{{ $item->id }}-description">Description</label>
              <textarea
                id="space-{{ $item->id }}-description"
                name="message"
                placeholder="Description"
                rows="3"
              >{{ data_get($formValues, 'message') }}</textarea>
            </div>

            @include('public.submissions._consent', ['privacyChecked' => data_get($formValues, 'privacy', false)])
<button class="event-space-submit" type="submit">
              <span>{{ __('cms.submit_request') }}</span>
              <svg
                class="event-space-submit-arrow"
                viewBox="0 0 24 24"
                aria-hidden="true"
              >
                <path
                  d="M5 12h14M13 6l6 6-6 6"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                />
              </svg>
            </button>
          </form>
          @if(($dialog ?? false) && $item->booking_mode->allowsExternal() && $item->external_url)
              <a href="{{ $item->external_url }}" target="_blank" rel="noopener" class="public-button mt-4">{{ __('cms.external_form') }}</a>
          @endif
        </div>
    </div>
      </section>

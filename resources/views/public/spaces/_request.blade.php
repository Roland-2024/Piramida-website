@php
    $formValues = !($dialog ?? false) || (string) old('_space_id') === (string) $item->id ? old() : [];
@endphp
<section class="event-space-section">
    <div class="request-frame">
        <div class="event-space-shape" aria-hidden="true"></div>

        @if($dialog ?? false)
        <button type="button" class="event-space-close" data-dialog-close="space-request-{{ $item->id }}" aria-label="{{ __('cms.close') }}"><img src="{{ \App\Support\WebsiteContent::image('template/images/Cross.svg') }}" alt=""></button>
        @else
        <a href="{{ route('public.spaces.index', app()->getLocale()) }}"
          class="event-space-close"
          type="button"
          aria-label="{{ __('website.spaces__request_close_booking_form') }}"
          data-close-event-modal
        >
          <img
            src="{{ \App\Support\WebsiteContent::image('template/images/Cross.svg') }}"
            alt=""
            class="h-7 w-7 object-contain"
          />
        </a>
        @endif

        <div class="event-space-card">
          @if(!($dialog ?? false))<a href="{{ route('public.spaces.index', app()->getLocale()) }}"
            class="event-space-close event-space-close-mobile"
            type="button"
            aria-label="{{ __('website.spaces__request_close_booking_form') }}"
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
                <label class="sr-only" for="space-{{ $item->id }}-fullName">{{ __('website.spaces__request_full_name_151') }}</label>
                <input
                  id="space-{{ $item->id }}-fullName"
                  name="name" value="{{ data_get($formValues, 'name') }}"
                  type="text"
                  autocomplete="name"
                  placeholder="{{ __('website.spaces__request_full_name') }}"
                  required
                />
              </div>

              <div class="event-space-field">
                <label class="sr-only" for="space-{{ $item->id }}-email">{{ __('website.spaces__request_email_address_152') }}</label>
                <input
                  id="space-{{ $item->id }}-email"
                  name="email" value="{{ data_get($formValues, 'email') }}"
                  type="email"
                  autocomplete="email"
                  placeholder="{{ __('website.spaces__request_email_address') }}"
                  required
                />
              </div>
            </div>

            <div class="event-space-row">
              <div class="event-space-field">
                <label class="sr-only" for="space-{{ $item->id }}-guests">{{ __('website.spaces__request_number_of_guests_153') }}</label>
                <input
                  id="space-{{ $item->id }}-guests"
                  name="attendees" value="{{ data_get($formValues, 'attendees') }}"
                  type="number"
                  min="1"
                  inputmode="numeric"
                  placeholder="{{ __('website.spaces__request_number_of_guests') }}"
                  required
                />
              </div>

              <div class="event-space-field">
                <label class="sr-only" for="space-{{ $item->id }}-phone">{{ __('website.spaces__request_phone_number_154') }}</label>
                <input
                  id="space-{{ $item->id }}-phone"
                  name="phone" value="{{ data_get($formValues, 'phone') }}"
                  type="tel"
                  autocomplete="tel"
                  inputmode="tel"
                  placeholder="{{ __('website.spaces__request_phone_number') }}"
                  required
                />
              </div>
            </div>

            <div class="event-space-field">
              <label class="sr-only" for="space-{{ $item->id }}-eventType">{{ __('website.spaces__request_event_type') }}</label>
              <div class="event-space-select-wrapper">
                <select id="space-{{ $item->id }}-eventType" name="event_type" required>
                  <option value="">{{ __('website.spaces__request_event_type_156') }}</option>
                  <option value="conference" @selected(data_get($formValues, 'event_type') === 'conference')>{{ __('website.spaces__request_conference') }}</option>
                  <option value="wedding" @selected(data_get($formValues, 'event_type') === 'wedding')>{{ __('website.spaces__request_wedding') }}</option>
                  <option value="corporate" @selected(data_get($formValues, 'event_type') === 'corporate')>{{ __('website.spaces__request_corporate_event') }}</option>
                  <option value="private" @selected(data_get($formValues, 'event_type') === 'private')>{{ __('website.spaces__request_private_event') }}</option>
                  <option value="other" @selected(data_get($formValues, 'event_type') === 'other')>{{ __('website.spaces__request_other') }}</option>
                </select>
                <span
                  class="event-space-select-arrow"
                  aria-hidden="true"
                ></span>
              </div>
            </div>

            <div class="event-space-row">
              <div class="event-space-field">
                <label class="sr-only" for="space-{{ $item->id }}-date">{{ __('website.spaces__request_preferred_date') }}</label>
                <input
                  id="space-{{ $item->id }}-date"
                  name="preferred_date" value="{{ data_get($formValues, 'preferred_date') }}"
                  type="date"
                  placeholder="{{ __('website.spaces__request_preferred_date') }}"
                  required
                />
              </div>

              <div class="event-space-field">
                <label class="sr-only" for="space-{{ $item->id }}-time">{{ __('website.spaces__request_preferred_time') }}</label>
                <input
                  id="space-{{ $item->id }}-time"
                  name="preferred_time" value="{{ data_get($formValues, 'preferred_time') }}"
                  type="time"
                  placeholder="{{ __('website.spaces__request_preferred_time') }}"
                  required
                />
              </div>
            </div>

            <div class="event-space-field">
              <label class="sr-only" for="space-{{ $item->id }}-description">{{ __('website.spaces__request_description') }}</label>
              <textarea
                id="space-{{ $item->id }}-description"
                name="message"
                placeholder="{{ __('website.spaces__request_description') }}"
                rows="3"
              >{{ data_get($formValues, 'message') }}</textarea>
            </div>

            @include('public.submissions._consent', ['privacyChecked' =>data_get($formValues, 'privacy', false)])<button class="event-space-submit" type="submit">
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
          @if(($dialog ?? false) && $item->booking_mode->allowsExternal() && $item->external_url)<a href="{{ $item->external_url }}" target="_blank" rel="noopener" class="public-button mt-4">{{ __('cms.external_form') }}</a>
          @endif
        </div>
    </div>
      </section>

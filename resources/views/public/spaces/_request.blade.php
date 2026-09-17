<section class="event-space-section">
        <div class="event-space-shape" aria-hidden="true"></div>

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

        <div class="event-space-card">
          <a href="{{ route('public.spaces.index', app()->getLocale()) }}"
            class="event-space-close event-space-close-mobile"
            type="button"
            aria-label="Close booking form"
            data-close-event-modal
          >
            <span aria-hidden="true"></span>
          </a>

          <h1 id="book-event-space-title" class="event-space-title">
            {{ $translation->title }}
          </h1>
          <p class="event-space-subtitle hidden md:block">
            {{ __('cms.request_confirmation_notice') }}
          </p>

          <form class="event-space-form" action="{{ route('public.spaces.event-request', [app()->getLocale(), $translation->slug]) }}" method="post">
            @csrf
@include('public.submissions._feedback')
<div class="event-space-row">
              <div class="event-space-field">
                <label class="sr-only" for="fullName">Full name</label>
                <input
                  id="fullName"
                  name="name" value="{{ old('name') }}"
                  type="text"
                  autocomplete="name"
                  placeholder="Full Name*"
                  required
                />
              </div>

              <div class="event-space-field">
                <label class="sr-only" for="email">Email Address</label>
                <input
                  id="email"
                  name="email" value="{{ old('email') }}"
                  type="email"
                  autocomplete="email"
                  placeholder="Email Address*"
                  required
                />
              </div>
            </div>

            <div class="event-space-row">
              <div class="event-space-field">
                <label class="sr-only" for="guests">Number of Guests</label>
                <input
                  id="guests"
                  name="attendees" value="{{ old('attendees') }}"
                  type="number"
                  min="1"
                  inputmode="numeric"
                  placeholder="Number of Guests*"
                  required
                />
              </div>

              <div class="event-space-field">
                <label class="sr-only" for="phone">Phone Number</label>
                <input
                  id="phone"
                  name="phone" value="{{ old('phone') }}"
                  type="tel"
                  autocomplete="tel"
                  inputmode="tel"
                  placeholder="Phone Number*"
                  required
                />
              </div>
            </div>

            <div class="event-space-field">
              <label class="sr-only" for="eventType">Event type</label>
              <div class="event-space-select-wrapper">
                <select id="eventType" name="event_type" required>
                  <option value="">Event type*</option>
                  <option value="conference" @selected(old('event_type') === 'conference')>Conference</option>
                  <option value="wedding" @selected(old('event_type') === 'wedding')>Wedding</option>
                  <option value="corporate" @selected(old('event_type') === 'corporate')>Corporate Event</option>
                  <option value="private" @selected(old('event_type') === 'private')>Private Event</option>
                  <option value="other" @selected(old('event_type') === 'other')>Other</option>
                </select>
                <span
                  class="event-space-select-arrow"
                  aria-hidden="true"
                ></span>
              </div>
            </div>

            <div class="event-space-row">
              <div class="event-space-field">
                <label class="sr-only" for="date">Preferred Date</label>
                <input
                  id="date"
                  name="preferred_date" value="{{ old('preferred_date') }}"
                  type="date"
                  placeholder="Preferred Date"
                  required
                />
              </div>

              <div class="event-space-field">
                <label class="sr-only" for="time">Preferred Time</label>
                <input
                  id="time"
                  name="preferred_time" value="{{ old('preferred_time') }}"
                  type="time"
                  placeholder="Preferred Time"
                  required
                />
              </div>
            </div>

            <div class="event-space-field">
              <label class="sr-only" for="description">Description</label>
              <textarea
                id="description"
                name="message"
                placeholder="Description"
                rows="3"
              >{{ old('message') }}</textarea>
            </div>

            @include('public.submissions._consent')
<button class="event-space-submit" type="submit">
              <span>Submit Request</span>
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
        </div>
      </section>

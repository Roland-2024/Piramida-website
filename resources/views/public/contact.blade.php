<x-layouts.public :title="__('cms.contact')" :language-urls="$languageUrls" :styles="['contactus']">
<section class="contact-page" aria-labelledby="contact-title">
        <h1 id="contact-title" class="contact-title">
          <span>{{ __('cms.contact_title_first') }}</span><span>{{ __('cms.contact_title_second') }}</span>
        </h1>

        <section class="contact-layout">
          <div class="contact-info">
            <h2>{{ __('cms.lets_get_in_touch') }}</h2>
            <p class="contact-intro">
              {{ __('cms.contact_intro') }}
            </p>

            <div class="contact-details">
              <div>
                <h3>{{ __('cms.contact') }}</h3>
                <a href="mailto:{{ $siteSettings?->email }}">{{ $siteSettings?->email }}</a>
                <a href="tel:{{ $siteSettings?->phone }}">{{ $siteSettings?->phone }}</a>
              </div>

              <div>
                <h3>{{ __('cms.location') }}</h3>
                <p>{{ $siteSettings?->translation()?->address }}</p>
              </div>
            </div>

            <div class="contact-socials" aria-label="Social links">
              <a href="{{ $siteSettings?->facebook_url ?: '#' }}" aria-label="Facebook">
                <img src="/template/images/facebook.svg" alt="" />
              </a>
              <a href="{{ $siteSettings?->instagram_url ?: '#' }}" aria-label="Instagram">
                <img src="/template/images/Ig.svg" alt="" />
              </a>
            </div>
          </div>

          <div class="contact-form-shell">
            <form class="contact-form" action="{{ route('public.contact.store', app()->getLocale()) }}" method="post">
              @csrf
@include('public.submissions._feedback')
<h2>{{ __('cms.contact') }}</h2>
              <p>
                We're always happy to connect. Reach out and our team will get
                back to you as soon as possible.
              </p>

              <div class="contact-form-row">
                <label>
                  <span>{{ __('cms.name') }}</span>
                  <input name="name" type="text" required maxlength="255" value="{{ old('name') }}" placeholder="{{ __('cms.name') }}" />
                </label>

                <label>
                  <span>{{ __('cms.email') }}</span>
                  <input
                    name="email" required maxlength="255" value="{{ old('email') }}"
                    type="email"
                    placeholder="{{ __('cms.email') }}"
                  />
                </label>
              </div>

              <label>
                <span>{{ __('cms.message') }}</span>
                <textarea name="message" rows="4" required>{{ old('message') }}</textarea>
              </label>

              @include('public.submissions._consent')
<button type="submit">
                <span>{{ __('cms.submit_request') }}</span>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path
                    d="M5 12h14M13 6l6 6-6 6"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
              </button>
            </form>
          </div>
        </section>
      </section>
<section class="contact-map" aria-label="Piramida location map">
      <div class="contact-map-frame" aria-hidden="true">
        <iframe
          src="{{ $siteSettings?->map_url ?: 'https://maps.google.com/maps?ll=41.3275,19.8187&z=16&t=m&hl=en&output=embed' }}"
          title="Piramida Tirana map"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
      </div>

      <a
        class="contact-map-marker"
        href="https://www.google.com/maps/search/?api=1&query=Piramida+Tirana"
        target="_blank"
        rel="noreferrer"
        aria-label="Open Piramida Tirana in Google Maps"
      >
        <span class="contact-map-pin" aria-hidden="true"></span>
        <img src="/template/images/logo piramida.svg" alt="" />
        <span>Google Map</span>
      </a>
    </section>
</x-layouts.public>

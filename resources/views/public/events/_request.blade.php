<section id="event-request" data-request-panel data-feedback="{{ session('success') || $errors->any() ? 'true' : 'false' }}">

        <section class="registration-section">
          <!-- Decorative green background -->
          <div class="registration-shape" aria-hidden="true"></div>

          <!-- Close button -->
          <button
            data-request-close class="registration-close"
            type="button"
            aria-label="Close registration form"
          >
            <img
              src="/template/images/Cross.svg"
              alt=""
              class="h-7 w-7 object-contain"
            />
          </button>

          <!-- White form card -->
          <div class="registration-card">
            <button
              data-request-close class="registration-close registration-close-mobile"
              type="button"
              aria-label="Close registration form"
            >
              <span aria-hidden="true"></span>
            </button>

            <div class="registration-title">{{ __('cms.join_us') }}</div>
            <p class="template-request-notice">{{ __('cms.request_confirmation_notice') }}</p><form class="registration-form" method="POST" action="{{ route('public.events.request', [app()->getLocale(), $translation->slug]) }}">@csrf
@include('public.submissions._feedback')
              <div class="registration-field">
                <label class="sr-only" for="fullName">Full name</label>

                <input
                  id="fullName"
                  name="name" value="{{ old('name') }}"
                  type="text"
                  placeholder="Full Name*"
                  required
                />
              </div>

              <div class="registration-field">
                <label class="sr-only" for="email">Email</label>

                <input
                  id="email"
                  name="email" value="{{ old('email') }}"
                  type="email"
                  placeholder="Email"
                  required
                />
              </div>

              <div class="registration-field registration-phone-field">
                <label class="sr-only" for="phone">Phone</label>

                <input
                  id="phone"
                  name="phone" value="{{ old('phone') }}"
                  type="tel"
                  placeholder="Phone"
                  required
                />

                <span
                  class="registration-field-arrow"
                  aria-hidden="true"
                ></span>
              </div>

              <label class="registration-terms">
                <input type="checkbox" name="privacy" value="1" @checked(old('privacy')) required />

                <span class="registration-checkbox" aria-hidden="true"></span>

                <span class="md:text-[16px] text-[14px]">
                  {{ __('cms.privacy_consent') }}
                </span>
              </label>

              <button class="registration-submit" type="submit">
                <span class="md:text-[18px] text-[16px]">{{ __('cms.submit_request') }}</span>

                <svg
                  class="registration-submit-arrow"
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
            <div hidden><input name="website" tabindex="-1" autocomplete="off"></div></form>
          </div>
        </section>

</section>

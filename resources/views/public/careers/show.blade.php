<x-layouts.public :title="$translation->title" :language-urls="$languageUrls" :styles="['job-application']" :footer="false">

        <section class="job-application-section">
          <div class="job-application-shape" aria-hidden="true"></div>

          <a href="{{ route('public.careers.index', app()->getLocale()) }}"
            class="job-application-close"
            type="button"
            aria-label="Close job application form"
          >
            <img
              src="/template/images/Cross.svg"
              alt=""
              class="h-7 w-7 object-contain"
            />
          </a>

          <div class="job-application-card">
            <a href="{{ route('public.careers.index', app()->getLocale()) }}"
              class="job-application-close-mobile"
              type="button"
              aria-label="Close job application form"
            >
              <span aria-hidden="true"></span>
            </a>

            <h1 class="job-application-title">{{ $translation->title }}</h1>
            <p class="job-application-subtitle">
              Please fill out the form to submit job application
            </p>

            @if ($item->booking_mode->allowsInternal())<form class="job-application-form" action="{{ route('public.careers.apply', [app()->getLocale(), $translation->slug]) }}" enctype="multipart/form-data" method="post">
              @csrf
@include('public.submissions._feedback')
<div class="job-application-row">
                <div class="job-application-field">
                  <label class="sr-only" for="firstName">First Name</label>
                  <input
                    id="firstName"
                    name="first_name" value="{{ old('first_name') }}"
                    type="text"
                    autocomplete="given-name"
                    placeholder="First Name*"
                    required
                  />
                </div>

                <div class="job-application-field">
                  <label class="sr-only" for="lastName">Last Name</label>
                  <input
                    id="lastName"
                    name="last_name" value="{{ old('last_name') }}"
                    type="text"
                    autocomplete="family-name"
                    placeholder="Last Name*"
                    required
                  />
                </div>
              </div>

              <div class="job-application-field">
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

              <div class="job-application-field">
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

              <label class="job-application-upload" for="resume">
                <span>Upload your resume</span>
                <svg
                  class="job-application-upload-icon"
                  viewBox="0 0 24 24"
                  aria-hidden="true"
                >
                  <path
                    d="M8.5 17.5H7.75a4.25 4.25 0 0 1-.8-8.42 5.5 5.5 0 0 1 10.55 1.7 3.5 3.5 0 0 1-.5 6.97h-.5"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M12 20v-7M9.25 15.75 12 13l2.75 2.75"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.7"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
                <input
                  id="resume"
                  name="attachment" required
                  type="file"
                  accept=".pdf,.doc,.docx"
                />
              </label>

              <div class="job-application-field">
                <label class="sr-only" for="message">Message</label>
                <textarea
                  id="message"
                  name="message"
                  placeholder="Message"
                  rows="2"
                >{{ old('message') }}</textarea>
              </div>

              @include('public.submissions._consent')
<button class="job-application-submit" type="submit">
                <span>Submit</span>
                <svg
                  class="job-application-submit-arrow"
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
            </form>@endif
@if ($item->booking_mode->allowsExternal() && $item->external_url)<a href="{{ $item->external_url }}" rel="noopener" target="_blank" class="public-button">{{ __('cms.external_form') }}</a>@endif
          </div>
        </section>

</x-layouts.public>

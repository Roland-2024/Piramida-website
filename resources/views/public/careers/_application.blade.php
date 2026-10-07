<section class="job-application-section">
    <div class="request-frame">
          <div class="job-application-shape" aria-hidden="true"></div>

          @if($dialog ?? false)<button type="button" data-dialog-close="career-{{ $item->id }}"
            class="job-application-close"
            aria-label="{{ __('website.careers__application_close_job_application_form') }}"
          >
            <img
              src="{{ \App\Support\WebsiteContent::image('template/images/Cross.svg') }}"
              alt=""
              class="h-7 w-7 object-contain"
            />
          </button>@else<a href="{{ route('public.careers.index', app()->getLocale()) }}"
            class="job-application-close"
            type="button"
            aria-label="{{ __('website.careers__application_close_job_application_form') }}"
          >
            <img
              src="{{ \App\Support\WebsiteContent::image('template/images/Cross.svg') }}"
              alt=""
              class="h-7 w-7 object-contain"
            />
          </a>@endif

          <div class="job-application-card">
            @if($dialog ?? false)<button type="button" data-dialog-close="career-{{ $item->id }}"
              class="job-application-close-mobile"
              aria-label="{{ __('website.careers__application_close_job_application_form') }}"
            >
              <span aria-hidden="true"></span>
            </button>@else<a href="{{ route('public.careers.index', app()->getLocale()) }}"
              class="job-application-close-mobile"
              type="button"
              aria-label="{{ __('website.careers__application_close_job_application_form') }}"
            >
              <span aria-hidden="true"></span>
            </a>@endif

            <h1 class="job-application-title">{{ $translation->title }}</h1>
            <p class="job-application-subtitle">{{ __('website.careers__application_please_fill_out_the_form_to_submit_job_application') }}</p>

            @if ($item->booking_mode->allowsInternal())<form class="job-application-form" action="{{ route('public.careers.apply', [app()->getLocale(), $translation->slug]) }}" enctype="multipart/form-data" method="post">
              @csrf
<input type="hidden" name="_career_id" value="{{ $item->id }}">
@if(!($dialog ?? false) || (string) old('_career_id') === (string) $item->id)
@include('public.submissions._feedback')
@endif
<div class="job-application-row">
                <div class="job-application-field">
                  <label class="sr-only" for="career-{{ $item->id }}-firstName">{{ __('website.careers__application_first_name_51') }}</label>
                  <input
                    id="career-{{ $item->id }}-firstName"
                    name="first_name" value="{{ old('first_name') }}"
                    type="text"
                    autocomplete="given-name"
                    placeholder="{{ __('website.careers__application_first_name') }}"
                    required
                  />
                </div>

                <div class="job-application-field">
                  <label class="sr-only" for="career-{{ $item->id }}-lastName">{{ __('website.careers__application_last_name_52') }}</label>
                  <input
                    id="career-{{ $item->id }}-lastName"
                    name="last_name" value="{{ old('last_name') }}"
                    type="text"
                    autocomplete="family-name"
                    placeholder="{{ __('website.careers__application_last_name') }}"
                    required
                  />
                </div>
              </div>

              <div class="job-application-field">
                <label class="sr-only" for="career-{{ $item->id }}-email">{{ __('website.careers__application_email_address_53') }}</label>
                <input
                  id="career-{{ $item->id }}-email"
                  name="email" value="{{ old('email') }}"
                  type="email"
                  autocomplete="email"
                  placeholder="{{ __('website.careers__application_email_address') }}"
                  required
                />
              </div>

              <div class="job-application-field">
                <label class="sr-only" for="career-{{ $item->id }}-phone">{{ __('website.careers__application_phone_number_54') }}</label>
                <input
                  id="career-{{ $item->id }}-phone"
                  name="phone" value="{{ old('phone') }}"
                  type="tel"
                  autocomplete="tel"
                  inputmode="tel"
                  placeholder="{{ __('website.careers__application_phone_number') }}"
                  required
                />
              </div>

              <label class="job-application-upload" for="career-{{ $item->id }}-resume">
                <span>{{ __('website.careers__application_upload_your_resume') }}</span>
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
                  id="career-{{ $item->id }}-resume"
                  name="attachment" required
                  type="file"
                  accept=".pdf,.doc,.docx"
                />
              </label>

              <div class="job-application-field">
                <label class="sr-only" for="career-{{ $item->id }}-message">{{ __('website.careers__application_message') }}</label>
                <textarea
                  id="career-{{ $item->id }}-message"
                  name="message"
                  placeholder="{{ __('website.careers__application_message') }}"
                  rows="2"
                >{{ old('message') }}</textarea>
              </div>

              @include('public.submissions._consent')
<button class="job-application-submit" type="submit">
                <span>{{ __('website.careers__application_submit') }}</span>
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
    </div>
        </section>

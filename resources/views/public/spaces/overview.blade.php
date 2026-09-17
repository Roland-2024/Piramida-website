<x-layouts.public :title="__('cms.rent_a_space')" :language-urls="$languageUrls" :styles="['rent-space']" :footer="false"><section class="rent-space-main">
        <h1 class="rent-space-title">{{ __('cms.rent_a_space') }}</h1>

        <section class="rent-space-card-list" aria-label="Rentable spaces">
          <a class="rent-space-card" href="{{ route('public.spaces.index', [app()->getLocale(), 'type' => 'event_space']) }}">
            <img
              class="rent-space-card-image rent-space-card-image-event"
              src="/template/images/rent-event-spaces.jpg"
              alt=""
            />

            <span class="rent-space-card-shade" aria-hidden="true"></span>

            <span class="rent-space-card-title">
              {{ __('cms.event_spaces') }}
            </span>
          </a>

          <a class="rent-space-card" href="{{ route('public.spaces.index', [app()->getLocale(), 'type' => 'leasing']) }}">
            <img
              class="rent-space-card-image rent-space-card-image-leasing"
              src="/template/images/rent-leasing.jpg"
              alt=""
            />

            <span class="rent-space-card-shade" aria-hidden="true"></span>

            <span class="rent-space-card-title">{{ __('cms.leasing') }}</span>
          </a>
        </section>
      </section></x-layouts.public>

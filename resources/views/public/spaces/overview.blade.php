<x-layouts.public :title="__('cms.rent_a_space')" :language-urls="$languageUrls" :styles="['rent-space']" :footer="false"><section class="rent-space-main">
        <h1 class="rent-space-title">{{ __('cms.rent_a_space') }}</h1>

        <section class="rent-space-card-list" aria-label="{{ __('website.spaces_overview_rentable_spaces') }}">
          <a class="rent-space-card" href="{{ route('public.spaces.index', app()->getLocale()) }}">
            <x-public-image
              class="rent-space-card-image rent-space-card-image-event"
              :path="'template/images/rent-event-spaces.jpg'"
              alt="" loading="lazy" />

            <span class="rent-space-card-shade" aria-hidden="true"></span>

            <span class="rent-space-card-title">
              {{ __('cms.event_spaces') }}
            </span>
          </a>

          <a class="rent-space-card" href="{{ route('public.leasing.index', app()->getLocale()) }}">
            <x-public-image
              class="rent-space-card-image rent-space-card-image-leasing"
              :path="'template/images/rent-leasing.jpg'"
              alt="" loading="lazy" />

            <span class="rent-space-card-shade" aria-hidden="true"></span>

            <span class="rent-space-card-title">{{ __('cms.leasing') }}</span>
          </a>
        </section>
      </section></x-layouts.public>

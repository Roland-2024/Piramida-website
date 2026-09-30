<x-layouts.public :title="__('cms.leasing_map')" :language-urls="$languageUrls" :styles="['piramida-map']" body-class="event-background-page min-h-screen overflow-x-hidden bg-[#000929] text-white">

        <section
          class="piramida-map-section bg-gradient-to-b from-transparent to-black"
          aria-labelledby="map-title"
        >
          <h1 id="map-title" class="sr-only">{{ __('cms.leasing_map') }}</h1>

          <div class="piramida-map-stage">
            <div class="piramida-map-frame">
              <img
                src="{{ asset('template/images/leasing/Piramida_map.png') }}"
                alt="Front view of the Piramida building"
                class="piramida-map-image"
              />

              <!-- Connector lines: viewBox matches the frame aspect ratio -->
              <svg
                class="piramida-map-lines"
                viewBox="0 0 1000 576"
                aria-hidden="true"
              >
                <g data-line="roof">
                  <path d="M500 181 V143" />
                  <circle cx="500" cy="143" r="5" />
                </g>
                <g data-line="third">
                  <path d="M574 255 H657" />
                  <circle cx="657" cy="255" r="5" />
                </g>
                <g data-line="ground">
                  <path d="M574 309 H648 V402" />
                  <circle cx="648" cy="402" r="5" />
                </g>

                <g data-line="exterior">
                  <path d="M426 414 H79" />
                  <circle cx="79" cy="414" r="5" />
                </g>
              </svg>

              <nav class="piramida-map-floors" aria-label="{{ __('cms.leasing_map') }}">
                <a
                  href="{{ route('public.leasing.floor', [app()->getLocale(), 'roof']) }}" class="piramida-map-floor" data-floor="roof">{{ $floors['roof'][app()->getLocale()] }}</a>
                <a
                  href="{{ route('public.leasing.floor', [app()->getLocale(), 'third']) }}" class="piramida-map-floor" data-floor="third">{{ $floors['third'][app()->getLocale()] }}</a>
                <a
                  href="{{ route('public.leasing.floor', [app()->getLocale(), 'ground']) }}" class="piramida-map-floor is-active" data-floor="ground">{{ $floors['ground'][app()->getLocale()] }}</a>

                <a
                  href="{{ route('public.leasing.floor', [app()->getLocale(), 'exterior']) }}" class="piramida-map-floor" data-floor="exterior">{{ $floors['exterior'][app()->getLocale()] }}</a>
              </nav>
            </div>
          </div>

          <!-- Fade to black, transitioning into footer -->
          <div
            class="md:block hidden h-[200px] w-full bg-gradient-to-b from-transparent to-black -mb-24 mt-[-1px] pointer-events-none"
          ></div>
        </section>

</x-layouts.public>

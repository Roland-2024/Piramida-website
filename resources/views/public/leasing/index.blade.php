<x-layouts.public :title="__('cms.leasing_map')" :language-urls="$languageUrls" :styles="['piramida-map']" body-class="event-background-page min-h-screen overflow-x-hidden bg-[#000929] text-white">

        <section
          class="piramida-map-section bg-gradient-to-b from-transparent to-black"
          aria-labelledby="map-title"
        >
          <h1 id="map-title" class="sr-only">{{ __('cms.leasing_map') }}</h1>

          <div class="piramida-map-stage">
            <div class="piramida-map-frame">
              <img
                src="{{ \App\Support\WebsiteContent::image('template/images/leasing/piramida-final.webp') }}"
                alt="{{ __('website.leasing_index_cutaway_render_of_the_piramida_building') }}"
                class="piramida-map-image"
              />

              <!-- Connector lines: viewBox matches the frame aspect ratio -->
              <svg
                class="piramida-map-lines piramida-map-lines--desktop"
                viewBox="0 0 1000 575"
                aria-hidden="true"
              >
                <g data-line="roof">
                  <path d="M500 177 V142" />
                  <circle cx="500" cy="142" r="5" />
                </g>
                <g data-line="third">
                  <path d="M575 250 H657" />
                  <circle cx="657" cy="250" r="5" />
                </g>
                <g data-line="ground">
                  <path d="M575 304 H651 V405" />
                  <circle cx="651" cy="405" r="5" />
                </g>
                <g data-line="exterior">
                  <path d="M425 412 H80" />
                  <circle cx="80" cy="412" r="5" />
                </g>
              </svg>

              <!-- Mobile connector lines: viewBox matches the 4:5 mobile frame,
                   endpoints follow the same building points after the crop -->
              <svg
                class="piramida-map-lines piramida-map-lines--mobile"
                viewBox="0 0 400 500"
                aria-hidden="true"
              >
                <g data-line="roof">
                  <path d="M200 155 V123" />
                  <circle cx="200" cy="123" r="4" />
                </g>
                <g data-line="third">
                  <path d="M268 218 H336" />
                  <circle cx="336" cy="218" r="4" />
                </g>
                <g data-line="ground">
                  <path d="M268 265 H331 V352" />
                  <circle cx="331" cy="352" r="4" />
                </g>
                <g data-line="exterior">
                  <path d="M132 358 H36" />
                  <circle cx="36" cy="358" r="4" />
                </g>
              </svg>

              <nav class="piramida-map-floors" aria-label="{{ __('cms.leasing_map') }}">
                <a
                  href="{{ route('public.leasing.floor', [app()->getLocale(), 'roof']) }}"
                  class="piramida-map-floor"
                  data-floor="roof"
                >{{ $floors['roof'][app()->getLocale()] }}</a>
                <a
                  href="{{ route('public.leasing.floor', [app()->getLocale(), 'third']) }}"
                  class="piramida-map-floor"
                  data-floor="third"
                >{{ $floors['third'][app()->getLocale()] }}</a>
                <a
                  href="{{ route('public.leasing.floor', [app()->getLocale(), 'ground']) }}"
                  class="piramida-map-floor is-active"
                  data-floor="ground"
                >{{ $floors['ground'][app()->getLocale()] }}</a>
                <a
                  href="{{ route('public.leasing.floor', [app()->getLocale(), 'exterior']) }}"
                  class="piramida-map-floor"
                  data-floor="exterior"
                >{{ $floors['exterior'][app()->getLocale()] }}</a>
              </nav>
            </div>
          </div>

          <!-- Fade to black, transitioning into footer -->
          <div
            class="md:block hidden h-[200px] w-full bg-gradient-to-b from-transparent to-black -mb-24 mt-[-1px] pointer-events-none"
          ></div>
        </section>
</x-layouts.public>

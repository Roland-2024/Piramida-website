<header class="site-header absolute top-0 left-0 w-full z-50 bg-transparent">
      <nav class="header-inner">
        <a href="{{ route('public.home', app()->getLocale()) }}" class="header-logo" aria-label="Piramida homepage">
          <img src="/template/images/logo piramida.svg" alt="Piramida logo" />
        </a>

        <button
          id="openMenu" data-dialog-open="mobileMenu" aria-haspopup="dialog"
          class="menu-open-button"
          type="button"
          aria-label="{{ __('cms.open_menu') }}"

          aria-controls="mobileMenu"
        >
          <img
            src="/template/images/menu icon.svg"
            alt=""
            class="h-8 w-8 object-contain"
          />
        </button>
      <div class="template-languages">@foreach (config('cms.locales') as $locale => $name)<a href="{{ $languageUrls[$locale] ?? route('public.home', $locale) }}" hreflang="{{ $locale === 'al' ? 'sq' : 'en' }}" @if(app()->getLocale() === $locale) aria-current="page" @endif>{{ strtoupper($locale) }}</a>@endforeach</div></nav>
    </header>

    <!-- Fullscreen menu -->
    <dialog id="mobileMenu" aria-label="{{ __('cms.navigation') }}" class="is-open">
      <!-- Close button -->
      <button
        id="closeMenu" data-dialog-close="mobileMenu" autofocus
        class="menu-close-button"
        type="button"
        aria-label="{{ __('cms.close') }}"
      >
        <img
          src="/template/images/Cross.svg"
          alt=""
          class="h-8 w-8 object-contain"
        />
      </button>

      <div class="menu-layout">
        <!-- Left side -->
        <section class="menu-left">
          <img
            src="/template/images/logo piramida.svg"
            alt="Piramida logo"
            class="menu-logo"
          />

          <nav class="menu-main-navigation" aria-label="Main navigation">
            <ul>
              <li>
                <a href="{{ $pageUrl('education') }}" class="mobile-main-link">{{ __('cms.education') }}</a>
              </li>

              <li>
                <a href="{{ route('public.home', app()->getLocale()).'#innovation' }}" class="mobile-main-link">{{ __('cms.innovation') }}</a>
              </li>

              <li>
                <a href="{{ route('public.businesses.index', app()->getLocale()) }}" class="mobile-main-link">{{ __('cms.business') }}</a>
              </li>

              <li>
                <a href="{{ route('public.home', app()->getLocale()).'#art' }}" class="mobile-main-link">{{ __('cms.art') }}</a>
              </li>

              <li>
                <a href="{{ route('public.events.index', app()->getLocale()) }}" class="mobile-main-link">{{ __('cms.events') }}</a>
              </li>

              <li>
                <a href="{{ route('public.attractions.index', app()->getLocale()) }}" class="mobile-main-link">{{ __('cms.attractions') }}</a>
              </li>

              <li>
                <span class="mobile-main-link" aria-disabled="true" title="{{ __('cms.museum_pending') }}">{{ __('cms.museum') }}</span>
              </li>

              <li>
                <a href="{{ route('public.spaces.overview', app()->getLocale()) }}" class="mobile-main-link">{{ __('cms.rent_a_space') }}</a>
              </li>
            </ul>
          </nav>
        </section>

        <!-- Right side -->
        <section class="menu-right">
          <div class="menu-right-content">
            <nav class="menu-secondary-links" aria-label="Secondary navigation">
              <a href="{{ $pageUrl('about-us') }}" class="mobile-small-link">{{ __('cms.about_us') }}</a>
              <a href="{{ route('public.news.index', app()->getLocale()) }}" class="mobile-small-link">{{ __('cms.news') }}</a>
              <a href="{{ route('public.careers.index', app()->getLocale()) }}" class="mobile-small-link">{{ __('cms.careers') }}</a>
              <a href="{{ route('public.contact', app()->getLocale()) }}" class="mobile-small-link">{{ __('cms.contact') }}</a>
            </nav>

            <div class="menu-bottom-content">
              <div class="menu-policy-links">
                <a href="{{ $pageUrl('terms-of-use') }}" class="mobile-footer-link">{{ __('cms.terms') }}</a>

                <a href="{{ $pageUrl('privacy-policy') }}" class="mobile-footer-link">{{ __('cms.privacy_policy') }}</a>
              </div>

              <div class="menu-social-links">
                <a href="{{ $siteSettings?->facebook_url ?: '#' }}" aria-label="Facebook" @if(!$siteSettings?->facebook_url) aria-disabled="true" tabindex="-1" @endif>
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      d="M14 8H17V4H14C10.7 4 9 6 9 9V11H6V15H9V22H13V15H16L17 11H13V9C13 8.3 13.3 8 14 8Z"
                      stroke="currentColor"
                      stroke-width="1.7"
                      stroke-linejoin="round"
                    />
                  </svg>
                </a>

                <a href="{{ $siteSettings?->instagram_url ?: '#' }}" aria-label="Instagram" @if(!$siteSettings?->instagram_url) aria-disabled="true" tabindex="-1" @endif>
                  <svg
                    viewBox="0 0 24 24"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <rect
                      x="3"
                      y="3"
                      width="18"
                      height="18"
                      rx="5"
                      stroke="currentColor"
                      stroke-width="1.7"
                    />

                    <circle
                      cx="12"
                      cy="12"
                      r="4"
                      stroke="currentColor"
                      stroke-width="1.7"
                    />

                    <circle cx="17.5" cy="6.5" r="1" fill="currentColor" />
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </section>
      </div>
    </dialog>

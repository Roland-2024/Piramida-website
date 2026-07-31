<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}Piramida</title>
    @if ($description)<meta name="description" content="{{ $description }}">@endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-50 text-slate-950 antialiased">
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-5 px-5 py-5 lg:px-8">
            <a href="{{ route('public.home', app()->getLocale()) }}" class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-xl bg-amber-400 text-lg font-black">P</span>
                <span class="text-lg font-semibold">Piramida</span>
            </a>
            <nav class="flex flex-wrap items-center gap-5 text-sm font-medium" aria-label="Public navigation">
                <a href="{{ route('public.home', app()->getLocale()) }}" class="hover:text-amber-700">{{ __('cms.home') }}</a>
                @foreach ($headerPages as $headerPage)
                    @if ($headerTranslation = $headerPage->translation(app()->getLocale(), false))
                        <a href="{{ route('public.pages.show', [app()->getLocale(), $headerTranslation->slug]) }}" class="hover:text-amber-700">{{ $headerTranslation->title }}</a>
                    @endif
                @endforeach
                <a href="{{ route('public.news.index', app()->getLocale()) }}" class="hover:text-amber-700">{{ __('cms.news') }}</a>
                <a href="{{ route('public.events.index', app()->getLocale()) }}" class="hover:text-amber-700">{{ __('cms.events') }}</a>
                <a href="{{ route('public.attractions.index', app()->getLocale()) }}" class="hover:text-amber-700">{{ __('cms.attractions') }}</a>
                <a href="{{ route('public.businesses.index', app()->getLocale()) }}" class="hover:text-amber-700">{{ __('cms.businesses') }}</a>
                <a href="{{ route('public.spaces.index', app()->getLocale()) }}" class="hover:text-amber-700">{{ __('cms.spaces') }}</a>
                <a href="{{ route('public.careers.index', app()->getLocale()) }}" class="hover:text-amber-700">{{ __('cms.careers') }}</a>
                <a href="{{ route('public.contact', app()->getLocale()) }}" class="hover:text-amber-700">{{ __('cms.contact') }}</a>
            </nav>
            <div class="flex rounded-lg border border-slate-200 p-1 text-xs font-semibold uppercase">
                @foreach (config('cms.locales') as $locale => $name)
                    <a href="{{ $languageUrls[$locale] ?? route('public.home', $locale) }}" hreflang="{{ $locale }}" class="rounded-md px-2.5 py-1.5 {{ app()->getLocale() === $locale ? 'bg-slate-950 text-white' : 'text-slate-500 hover:bg-slate-100' }}">{{ $locale }}</a>
                @endforeach
            </div>
        </div>
    </header>

    <main>{{ $slot }}</main>

    <footer class="mt-20 border-t border-slate-200 bg-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-5 py-10 text-sm text-slate-500 sm:grid-cols-3 lg:px-8">
            <div>
                <p class="font-semibold text-slate-950">Piramida</p>
                @if ($siteSettings?->translation()?->footer_text)<p class="mt-2">{{ $siteSettings->translation()->footer_text }}</p>@endif
                <p class="mt-3">© {{ now()->year }} Piramida</p>
            </div>
            <nav class="space-y-2" aria-label="Footer navigation">
                @foreach ($footerPages as $footerPage)
                    @if ($footerTranslation = $footerPage->translation(app()->getLocale(), false))
                        <a href="{{ route('public.pages.show', [app()->getLocale(), $footerTranslation->slug]) }}" class="block hover:text-slate-900">{{ $footerTranslation->title }}</a>
                    @endif
                @endforeach
            </nav>
            <div class="text-right">
                @if ($siteSettings?->translation()?->address)<p>{{ $siteSettings->translation()->address }}</p>@endif
                @if ($siteSettings?->email)<a href="mailto:{{ $siteSettings->email }}" class="block hover:text-slate-900">{{ $siteSettings->email }}</a>@endif
                @if ($siteSettings?->phone)<a href="tel:{{ $siteSettings->phone }}" class="block hover:text-slate-900">{{ $siteSettings->phone }}</a>@endif
                <a href="{{ route('login') }}" class="mt-1 block hover:text-slate-900">Administration</a>
            </div>
        </div>
    </footer>
</body>
</html>

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
                <a href="{{ route('public.news.index', app()->getLocale()) }}" class="hover:text-amber-700">{{ __('cms.news') }}</a>
                <a href="{{ route('public.events.index', app()->getLocale()) }}" class="hover:text-amber-700">{{ __('cms.events') }}</a>
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
        <div class="mx-auto flex max-w-7xl flex-wrap justify-between gap-4 px-5 py-8 text-sm text-slate-500 lg:px-8">
            <p>© {{ now()->year }} Piramida</p>
            <a href="{{ route('login') }}" class="hover:text-slate-900">Administration</a>
        </div>
    </footer>
</body>
</html>

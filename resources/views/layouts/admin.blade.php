<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-ui min-h-screen bg-slate-100 text-slate-900 antialiased">
    <a href="#admin-content" class="admin-skip">Skip to content</a>
    <div class="min-h-screen md:flex">
        <div data-sidebar-overlay class="fixed inset-0 z-30 hidden bg-slate-950/50 md:hidden"></div>

        <aside id="admin-navigation" data-sidebar class="admin-sidebar fixed inset-y-0 left-0 z-40 hidden w-72 flex-col bg-slate-950 text-white md:flex">
            <div class="flex h-18 items-center gap-3 border-b border-white/10 px-6">
                <img src="{{ asset('template/images/logo piramida.svg') }}" width="62" height="28" alt="Piramida">
                <div>
                    <p class="font-semibold">Piramida CMS</p>
                    <p class="text-xs text-slate-400">Content workspace</p>
                </div>
            </div>

            <button data-sidebar-close type="button" class="mx-4 mt-3 rounded-lg border border-white/20 p-2 text-sm md:hidden">Close navigation ×</button>
            <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto px-4 py-4" aria-label="Dashboard">
                <a href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">Dashboard</a>
                <x-admin.nav-group label="Website" :links="[
                    ['Pages', 'admin.pages', 'index'],
                    ['Page sections', 'admin.sections', 'index'],
                    ['Programs / Carousel posts', 'admin.programs', 'index'],
                    ['Website content', 'admin.website-content', 'edit'],
                ]" />
                <x-admin.nav-group label="Activities & careers" :links="[
                    ['News', 'admin.news', 'index'],
                    ['Events', 'admin.events', 'index'],
                    ['Careers', 'admin.careers', 'index'],
                ]" />
                <x-admin.nav-group label="Places & spaces" :links="[
                    ['Attractions', 'admin.attractions', 'index'],
                    ['Businesses', 'admin.businesses', 'index'],
                    ['Event spaces', 'admin.spaces', 'index'],
                    ['Leasing spaces', 'admin.leasing', 'index'],
                ]" />
                <a href="{{ route('admin.media.index') }}" @if(request()->routeIs('admin.media.*')) aria-current="page" @endif class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.media.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">Media</a>
                @can('manage-users')
                    <x-admin.nav-group label="Administration" :links="[
                        ['Users', 'admin.users', 'index'],
                        ['Site settings', 'admin.settings', 'edit'],
                    ]" />
                @endcan
                @can('view-submissions')
                    <a href="{{ route('admin.submissions.index') }}" @if(request()->routeIs('admin.submissions.*')) aria-current="page" @endif class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.submissions.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">Submissions</a>
                @endcan
            </nav>

            <div class="shrink-0 border-t border-white/10 p-4">
                <div class="mb-3 px-2">
                    <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400">{{ auth()->user()->role->label() }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full rounded-lg border border-white/10 px-3 py-2 text-left text-sm text-slate-300 hover:bg-white/5 hover:text-white" type="submit">
                        Sign out
                    </button>
                </form>
            </div>
        </aside>

        <div data-admin-shell class="min-w-0 flex-1 md:pl-72">
            <header class="sticky top-0 z-20 flex h-18 items-center justify-between border-b border-slate-200 bg-white/95 px-4 backdrop-blur sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button data-sidebar-toggle type="button" class="rounded-lg border border-slate-200 p-2 text-slate-600 md:hidden" aria-label="Open navigation" aria-controls="admin-navigation" aria-expanded="false">
                        <span aria-hidden="true">☰</span>
                    </button>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Administration</p>
                        <h1 class="font-semibold">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                </div>
                <div class="flex shrink-0 items-center gap-5"><span class="hidden text-xs text-slate-500 lg:block">{{ now()->format('d M Y') }}</span><a href="{{ url('/') }}" target="_blank" rel="noopener" class="admin-website-link">View website ↗<span class="sr-only"> (opens in a new tab)</span></a></div>
            </header>

            <main id="admin-content" tabindex="-1" class="admin-content p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        {{ session('warning') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        Please correct the highlighted fields.
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>

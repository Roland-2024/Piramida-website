<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <div class="min-h-screen md:flex">
        <div data-sidebar-overlay class="fixed inset-0 z-30 hidden bg-slate-950/50 md:hidden"></div>

        <aside data-sidebar class="fixed inset-y-0 left-0 z-40 hidden w-72 flex-col bg-slate-950 text-white md:flex">
            <div class="flex h-18 items-center gap-3 border-b border-white/10 px-6">
                <div class="flex size-9 items-center justify-center rounded-xl bg-amber-400 font-black text-slate-950">P</div>
                <div>
                    <p class="font-semibold">Piramida CMS</p>
                    <p class="text-xs text-slate-400">Content administration</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6" aria-label="Dashboard">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.pages.index') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.pages.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    Pages
                </a>
                <a href="{{ route('admin.sections.index') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.sections.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    Page sections
                </a>
                <a href="{{ route('admin.news.index') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.news.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    News
                </a>
                <a href="{{ route('admin.events.index') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.events.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    Events
                </a>
                <a href="{{ route('admin.attractions.index') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.attractions.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    Attractions
                </a>
                <a href="{{ route('admin.businesses.index') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.businesses.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    Businesses
                </a>
                <a href="{{ route('admin.spaces.index') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.spaces.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    Spaces
                </a>
                <a href="{{ route('admin.careers.index') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.careers.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    Careers
                </a>
                <a href="{{ route('admin.media.index') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.media.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    Media
                </a>

                @can('manage-users')
                    <a href="{{ route('admin.submissions.index') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.submissions.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        Submissions
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        Users
                    </a>
                    <a href="{{ route('admin.settings.edit') }}" class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs('admin.settings.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                        Site settings
                    </a>
                @endcan
            </nav>

            <div class="border-t border-white/10 p-4">
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

        <div class="min-w-0 flex-1 md:pl-72">
            <header class="sticky top-0 z-20 flex h-18 items-center justify-between border-b border-slate-200 bg-white/95 px-4 backdrop-blur sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button data-sidebar-toggle type="button" class="rounded-lg border border-slate-200 p-2 text-slate-600 md:hidden" aria-label="Open navigation">
                        <span aria-hidden="true">☰</span>
                    </button>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Administration</p>
                        <h1 class="font-semibold">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                </div>
                <span class="hidden text-sm text-slate-500 sm:block">{{ now()->format('d M Y') }}</span>
            </header>

            <main class="p-4 sm:p-6 lg:p-8">
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sign in' }} · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-900 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex size-12 items-center justify-center rounded-2xl bg-amber-400 text-xl font-black text-slate-950">P</div>
                <h1 class="text-2xl font-semibold text-white">Piramida CMS</h1>
                <p class="mt-2 text-sm text-slate-400">Administration dashboard</p>
            </div>

            <section class="rounded-2xl bg-white p-6 shadow-2xl shadow-black/30 sm:p-8">
                @if (session('status'))
                    <div class="mb-5 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                {{ $slot }}
            </section>
        </div>
    </main>
</body>
</html>

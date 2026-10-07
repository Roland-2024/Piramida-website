<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Sign in' }} · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-ui admin-auth min-h-screen bg-slate-950 text-slate-900 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <img src="{{ asset('template/images/logo piramida.svg') }}" width="100" height="44" alt="Piramida" class="mx-auto mb-6">
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

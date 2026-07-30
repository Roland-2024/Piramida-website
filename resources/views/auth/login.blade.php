<x-layouts.auth title="Sign in">
    <h2 class="text-xl font-semibold">Welcome back</h2>
    <p class="mt-1 text-sm text-slate-500">Sign in with your dashboard account.</p>

    <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium">Email address</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium">Password</label>
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-amber-700 hover:text-amber-800">Forgot password?</a>
            </div>
            <input
                id="password"
                name="password"
                type="password"
                required
                autocomplete="current-password"
                class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
            >
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input name="remember" type="checkbox" value="1" class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
            Remember me
        </label>

        <button type="submit" class="w-full rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 disabled:opacity-60">
            Sign in
        </button>
    </form>
</x-layouts.auth>

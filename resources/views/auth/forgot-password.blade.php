<x-layouts.auth title="Reset password">
    <h2 class="text-xl font-semibold">Reset your password</h2>
    <p class="mt-2 text-sm text-slate-500">Enter your email and we will send a password-reset link through the configured mail service.</p>

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
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
                autocomplete="email"
                class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
            Email reset link
        </button>

        <a href="{{ route('login') }}" class="block text-center text-sm font-medium text-amber-700 hover:text-amber-800">Back to sign in</a>
    </form>
</x-layouts.auth>

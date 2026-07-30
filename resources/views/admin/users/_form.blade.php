@php
    $editing = isset($account);
    $isSelf = $editing && auth()->user()->is($account);
@endphp

<div class="grid gap-6 lg:grid-cols-2">
    <div>
        <label for="name" class="block text-sm font-medium">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $account->name ?? '') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
        @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium">Email address</label>
        <input id="email" name="email" type="email" value="{{ old('email', $account->email ?? '') }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
        @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium">
            Password
            @if ($editing) <span class="font-normal text-slate-400">(leave blank to keep current)</span> @endif
        </label>
        <input id="password" name="password" type="password" {{ $editing ? '' : 'required' }} autocomplete="new-password" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
        @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium">Confirm password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" {{ $editing ? '' : 'required' }} autocomplete="new-password" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
    </div>

    <div>
        <label for="role" class="block text-sm font-medium">Role</label>
        @if ($isSelf)
            <input type="hidden" name="role" value="{{ $account->role->value }}">
            <input value="{{ $account->role->label() }}" disabled class="mt-2 block w-full rounded-lg border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-500">
            <p class="mt-2 text-xs text-slate-500">Your own role cannot be changed here.</p>
        @else
            <select id="role" name="role" required class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-200">
                @foreach ($roles as $role)
                    <option value="{{ $role->value }}" @selected(old('role', $account->role->value ?? 'editor') === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
        @endif
        @error('role') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-end">
        @if ($isSelf)
            <input type="hidden" name="is_active" value="{{ (int) $account->is_active }}">
            <div class="w-full rounded-lg border border-slate-200 bg-slate-100 px-4 py-3 text-sm text-slate-500">
                Your account is active. You cannot deactivate yourself.
            </div>
        @else
            <label class="flex w-full items-center gap-3 rounded-lg border border-slate-200 px-4 py-3">
                <input type="hidden" name="is_active" value="0">
                <input name="is_active" type="checkbox" value="1" @checked((bool) old('is_active', $account->is_active ?? true)) class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                <span>
                    <span class="block text-sm font-medium">Active account</span>
                    <span class="block text-xs text-slate-500">Inactive users cannot sign in.</span>
                </span>
            </label>
        @endif
        @error('is_active') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-8 flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 pt-6">
    <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium hover:bg-slate-50">Cancel</a>
    <button type="submit" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
        {{ $editing ? 'Save changes' : 'Create user' }}
    </button>
</div>

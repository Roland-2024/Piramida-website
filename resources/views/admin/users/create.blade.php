<x-layouts.admin title="Create user">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-amber-700 hover:text-amber-800">← Users</a>
        <h2 class="mt-2 text-2xl font-semibold tracking-tight">Create dashboard user</h2>
        <p class="mt-1 text-sm text-slate-500">Create an Admin or Editor account. There is no public registration.</p>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        @csrf
        @include('admin.users._form')
    </form>
</x-layouts.admin>

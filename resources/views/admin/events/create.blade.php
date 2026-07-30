<x-layouts.admin title="Create event">
    <div class="mb-6"><a href="{{ route('admin.events.index') }}" class="text-sm font-medium text-amber-700">← Events</a><h2 class="mt-2 text-2xl font-semibold">Create event</h2></div>
    <form method="POST" action="{{ route('admin.events.store') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">@csrf @include('admin.events._form')</form>
</x-layouts.admin>

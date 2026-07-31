<x-layouts.admin :title="'Create '.$singular">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold">Create {{ strtolower($singular) }}</h2>
        <p class="mt-1 text-sm text-slate-500">Complete both language versions before publishing.</p>
    </div>

    <form method="POST" action="{{ route("{$routePrefix}.store") }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @include('admin.catalog._form')
    </form>
</x-layouts.admin>

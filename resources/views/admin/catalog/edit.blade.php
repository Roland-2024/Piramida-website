<x-layouts.admin :title="'Edit '.$singular">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold">Edit {{ strtolower($singular) }}</h2>
        <p class="mt-1 text-sm text-slate-500">Update content, publication, and request options.</p>
    </div>

    <form method="POST" action="{{ route("{$routePrefix}.update", $item) }}" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        @include('admin.catalog._form')
    </form>
</x-layouts.admin>

<x-layouts.admin title="Create page">
    <div class="mb-6">
        <a href="{{ route('admin.pages.index') }}" class="text-sm font-medium text-amber-700">← Pages</a>
        <h2 class="mt-2 text-2xl font-semibold">Create page</h2>
    </div>
    <form method="POST" action="{{ route('admin.pages.store') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        @csrf
        @include('admin.pages._form')
    </form>
</x-layouts.admin>

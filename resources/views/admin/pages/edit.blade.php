<x-layouts.admin title="Edit page">
    <div class="mb-6">
        <a href="{{ route('admin.pages.show', $page) }}" class="text-sm font-medium text-amber-700">← View page</a>
        <h2 class="mt-2 text-2xl font-semibold">Edit {{ $page->translation('al')?->title }}</h2>
    </div>
    <form method="POST" action="{{ route('admin.pages.update', $page) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        @csrf
        @method('PUT')
        @include('admin.pages._form')
    </form>
</x-layouts.admin>

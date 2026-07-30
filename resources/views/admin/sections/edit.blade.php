<x-layouts.admin title="Edit page section">
    <div class="mb-6"><a href="{{ route('admin.sections.show', $section) }}" class="text-sm font-medium text-amber-700">← View section</a><h2 class="mt-2 text-2xl font-semibold">Edit {{ $section->internal_name }}</h2></div>
    <form method="POST" action="{{ route('admin.sections.update', $section) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">@csrf @method('PUT') @include('admin.sections._form')</form>
</x-layouts.admin>

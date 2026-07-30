<x-layouts.admin title="Create page section">
    <div class="mb-6"><a href="{{ route('admin.sections.index') }}" class="text-sm font-medium text-amber-700">← Page sections</a><h2 class="mt-2 text-2xl font-semibold">Create page section</h2></div>
    <form method="POST" action="{{ route('admin.sections.store') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">@csrf @include('admin.sections._form')</form>
</x-layouts.admin>

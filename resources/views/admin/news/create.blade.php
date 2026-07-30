<x-layouts.admin title="Create news article">
    <div class="mb-6"><a href="{{ route('admin.news.index') }}" class="text-sm font-medium text-amber-700">← News</a><h2 class="mt-2 text-2xl font-semibold">Create news article</h2></div>
    <form method="POST" action="{{ route('admin.news.store') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">@csrf @include('admin.news._form')</form>
</x-layouts.admin>

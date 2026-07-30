<x-layouts.admin title="Edit news article">
    <div class="mb-6"><a href="{{ route('admin.news.show', $article) }}" class="text-sm font-medium text-amber-700">← View article</a><h2 class="mt-2 text-2xl font-semibold">Edit {{ $article->translation('al')?->title }}</h2></div>
    <form method="POST" action="{{ route('admin.news.update', $article) }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">@csrf @method('PUT') @include('admin.news._form')</form>
</x-layouts.admin>

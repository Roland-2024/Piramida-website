<x-layouts.admin title="Upload media">
    <div class="mb-6"><a href="{{ route('admin.media.index') }}" class="text-sm font-medium text-amber-700">← Media</a><h2 class="mt-2 text-2xl font-semibold">Upload media</h2><p class="mt-1 text-sm text-slate-500">JPEG, PNG, WebP, GIF, or PDF up to 10 MB.</p></div>
    <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        @csrf
        <div><label for="file" class="block text-sm font-medium">File</label><input id="file" name="file" type="file" required accept=".jpg,.jpeg,.png,.webp,.gif,.pdf" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error('file')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <div><label for="alt_text_al" class="block text-sm font-medium">Alternative text · Shqip</label><input id="alt_text_al" name="alt_text_al" value="{{ old('alt_text_al') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
            <div><label for="alt_text_en" class="block text-sm font-medium">Alternative text · English</label><input id="alt_text_en" name="alt_text_en" value="{{ old('alt_text_en') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
        </div>
        <div class="mt-8 flex justify-end gap-3 border-t border-slate-200 pt-6"><a href="{{ route('admin.media.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium">Cancel</a><button class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white">Upload file</button></div>
    </form>
</x-layouts.admin>

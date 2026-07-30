@php $editing = isset($article); @endphp

<div class="grid gap-5 lg:grid-cols-4">
    <div><label class="block text-sm font-medium" for="status">Status</label><select id="status" name="status" required class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">@foreach ($statuses as $status)<option value="{{ $status->value }}" @selected(old('status', $article->status->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>@endforeach</select>@error('status')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="block text-sm font-medium" for="published_at">Publication date</label><input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', isset($article) ? $article->published_at?->format('Y-m-d\TH:i') : '') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error('published_at')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
    <div><label class="block text-sm font-medium" for="author_name">Author name</label><input id="author_name" name="author_name" value="{{ old('author_name', $article->author_name ?? '') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
    <div><label class="block text-sm font-medium" for="featured_media_id">Featured image</label><select id="featured_media_id" name="featured_media_id" class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm"><option value="">No image</option>@foreach ($mediaItems as $media)<option value="{{ $media->id }}" @selected((string) old('featured_media_id', $article->featured_media_id ?? '') === (string) $media->id)>{{ $media->original_name }}</option>@endforeach</select></div>
</div>

<div class="mt-8 space-y-5">
    @foreach ($locales as $locale => $localeName)
        @php $translation = isset($article) ? $article->translations->firstWhere('locale', $locale) : null; @endphp
        <details open class="rounded-xl border border-slate-200">
            <summary class="cursor-pointer px-5 py-4 font-semibold">{{ $localeName }} <span class="text-xs font-normal uppercase text-slate-400">{{ $locale }}</span></summary>
            <div class="grid gap-5 border-t border-slate-200 p-5 lg:grid-cols-2">
                <div><label class="block text-sm font-medium">Title</label><input name="translations[{{ $locale }}][title]" value="{{ old("translations.{$locale}.title", $translation?->title) }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error("translations.{$locale}.title")<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium">Slug</label><input name="translations[{{ $locale }}][slug]" value="{{ old("translations.{$locale}.slug", $translation?->slug) }}" placeholder="Generated from title when blank" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">@error("translations.{$locale}.slug")<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div class="lg:col-span-2"><label class="block text-sm font-medium">Excerpt</label><textarea name="translations[{{ $locale }}][excerpt]" rows="3" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old("translations.{$locale}.excerpt", $translation?->excerpt) }}</textarea></div>
                <div class="lg:col-span-2"><label class="block text-sm font-medium">Article content</label><textarea data-rich-text name="translations[{{ $locale }}][content]" rows="10" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 font-mono text-sm">{{ app(\App\Support\RichTextSanitizer::class)->sanitize(old("translations.{$locale}.content", $translation?->content)) }}</textarea></div>
                <div><label class="block text-sm font-medium">SEO title</label><input name="translations[{{ $locale }}][seo_title]" value="{{ old("translations.{$locale}.seo_title", $translation?->seo_title) }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm"></div>
                <div><label class="block text-sm font-medium">SEO description</label><textarea name="translations[{{ $locale }}][seo_description]" rows="2" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old("translations.{$locale}.seo_description", $translation?->seo_description) }}</textarea></div>
            </div>
        </details>
    @endforeach
</div>
<div class="mt-8 flex justify-end gap-3 border-t border-slate-200 pt-6"><a href="{{ route('admin.news.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium">Cancel</a><button class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white" type="submit">{{ $editing ? 'Save article' : 'Create article' }}</button></div>

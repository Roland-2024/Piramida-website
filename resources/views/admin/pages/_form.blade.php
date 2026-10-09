@php
    $editing = isset($page);
@endphp

@if ($editing && $page->carouselSlug())
    <x-admin.carousel-notice :page="$page" />
@endif

<div class="grid gap-6 lg:grid-cols-5">
    <div>
        <label for="status" class="block text-sm font-medium">Status</label>
        <select id="status" name="status" required class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $page->status->value ?? 'draft') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        @error('status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="published_at" class="block text-sm font-medium">Publication date</label>
        <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', isset($page) ? $page->published_at?->format('Y-m-d\TH:i') : '') }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        @error('published_at') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="display_order" class="block text-sm font-medium">Display order</label>
        <input id="display_order" name="display_order" type="number" min="0" value="{{ old('display_order', $page->display_order ?? 0) }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        @error('display_order') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <x-admin.image-picker :selected="$page->featured_media_id ?? null" />
    <div class="flex items-end">
        <label class="flex w-full items-center gap-3 rounded-lg border border-slate-200 px-4 py-3">
            <input type="hidden" name="is_homepage" value="0">
            <input name="is_homepage" type="checkbox" value="1" @checked((bool) old('is_homepage', $page->is_homepage ?? false)) class="rounded border-slate-300 text-amber-500">
            <span class="text-sm font-medium">Homepage</span>
        </label>
    </div>
</div>

<div class="mt-6">
    <label for="homepage_video_url" class="block text-sm font-medium">Homepage YouTube video</label>
    <input id="homepage_video_url" name="homepage_video_url" type="url" maxlength="2048" value="{{ old('homepage_video_url', $page->homepage_video_url ?? '') }}" placeholder="https://www.youtube.com/watch?v=..." aria-describedby="homepage_video_help" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
    <p id="homepage_video_help" class="mt-2 text-sm text-slate-500">Used by the homepage Play button in both languages. Leave blank to use the existing section video or About link.</p>
    @error('homepage_video_url') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div class="mt-8 space-y-5">
    @foreach ($locales as $locale => $localeName)
        @php
            $translation = isset($page) ? $page->translations->firstWhere('locale', $locale) : null;
        @endphp
        <details open class="rounded-xl border border-slate-200">
            <summary class="cursor-pointer px-5 py-4 font-semibold">{{ $localeName }} <span class="text-xs font-normal uppercase text-slate-400">{{ $locale }}</span></summary>
            <div class="grid gap-5 border-t border-slate-200 p-5 lg:grid-cols-2">
                <div>
                    <label for="title_{{ $locale }}" class="block text-sm font-medium">Title</label>
                    <input id="title_{{ $locale }}" name="translations[{{ $locale }}][title]" value="{{ old("translations.{$locale}.title", $translation?->title) }}" required class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                    @error("translations.{$locale}.title") <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="slug_{{ $locale }}" class="block text-sm font-medium">Slug</label>
                    <input id="slug_{{ $locale }}" name="translations[{{ $locale }}][slug]" value="{{ old("translations.{$locale}.slug", $translation?->slug) }}" @readonly($locale === 'en' && $editing && $page->hasTemplateSlug()) placeholder="Generated from title when blank" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                    @if ($locale === 'en' && $editing && $page->hasTemplateSlug())<p class="mt-2 text-xs text-slate-500">Locked template identifier. The title and content remain editable.</p>@endif
                    @error("translations.{$locale}.slug") <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="lg:col-span-2">
                    <label for="short_description_{{ $locale }}" class="block text-sm font-medium">Short description</label>
                    <textarea id="short_description_{{ $locale }}" name="translations[{{ $locale }}][short_description]" rows="3" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old("translations.{$locale}.short_description", $translation?->short_description) }}</textarea>
                    @error("translations.{$locale}.short_description") <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="lg:col-span-2">
                    <label for="content_{{ $locale }}" class="block text-sm font-medium">Main content</label>
                    <textarea data-rich-text id="content_{{ $locale }}" name="translations[{{ $locale }}][content]" rows="10" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 font-mono text-sm">{{ app(\App\Support\RichTextSanitizer::class)->sanitize(old("translations.{$locale}.content", $translation?->content)) }}</textarea>
                    @error("translations.{$locale}.content") <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="seo_title_{{ $locale }}" class="block text-sm font-medium">SEO title</label>
                    <input id="seo_title_{{ $locale }}" name="translations[{{ $locale }}][seo_title]" value="{{ old("translations.{$locale}.seo_title", $translation?->seo_title) }}" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label for="seo_description_{{ $locale }}" class="block text-sm font-medium">SEO description</label>
                    <textarea id="seo_description_{{ $locale }}" name="translations[{{ $locale }}][seo_description]" rows="2" class="mt-2 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old("translations.{$locale}.seo_description", $translation?->seo_description) }}</textarea>
                </div>
            </div>
        </details>
    @endforeach
</div>

<div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
    <a href="{{ route('admin.pages.index') }}" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium hover:bg-slate-50">Cancel</a>
    <button type="submit" class="rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ $editing ? 'Save page' : 'Create page' }}</button>
</div>

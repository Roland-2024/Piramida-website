@props(['mediaItems', 'selected' => [], 'label' => 'Gallery images'])
@php
    $images = $mediaItems->filter(fn ($media) => $media->disk === 'public' && str_starts_with($media->mime_type, 'image/'));
    $ids = array_map('strval', (array) old('gallery_media_ids', $selected));
@endphp
<div data-gallery-picker data-upload-url="{{ route('admin.media.store') }}">
    <p class="mb-2 text-sm font-medium">{{ $label }}</p>
    <input type="hidden" name="gallery_media_ids" value="">
    <div data-gallery-selected class="gallery-picker-grid">
        @foreach($ids as $id)
            @if($image = $images->firstWhere('id', $id))
                <div><img src="{{ $image->url() }}" alt="{{ $image->original_name }}" loading="lazy"><input type="hidden" name="gallery_media_ids[]" value="{{ $id }}"></div>
            @endif
        @endforeach
    </div>
    <button type="button" data-gallery-open class="mt-3 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium">Add images</button>
    <p class="mt-2 text-xs text-slate-500">Only this record’s selected images appear here. Move images with the arrows. Removal detaches an image; it does not delete the file. Save the record to apply changes.</p>
    <p data-gallery-status role="status" class="mt-2 text-sm text-red-700"></p>
    @error('gallery_media_ids')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    @error('gallery_media_ids.*')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    <dialog data-gallery-dialog class="gallery-picker-dialog" aria-label="Select gallery images">
        <div class="flex items-center justify-between gap-4 border-b p-5">
            <h2 class="text-xl font-semibold">Media library</h2>
            <button type="button" data-gallery-close aria-label="Close media library" class="rounded border px-3 py-2">Close</button>
        </div>
        <div class="space-y-4 p-5">
            <label class="block text-sm">Search images<input data-gallery-search type="search" class="mt-1 block w-full rounded-lg border border-slate-300 p-3"></label>
            <label class="block text-sm">Upload images (JPG, PNG, WebP, GIF; max 10 MB each)<input data-gallery-upload type="file" multiple accept="image/jpeg,image/png,image/webp,image/gif" class="mt-2 block w-full"></label>
            <p data-gallery-upload-status role="status" class="text-sm text-slate-600"></p>
            <div data-gallery-library class="gallery-picker-grid">
                @foreach($images as $image)
                    <label data-media-id="{{ $image->id }}" data-media-name="{{ $image->original_name }}" data-media-url="{{ $image->url() }}" class="gallery-library-item">
                        <img src="{{ $image->url() }}" alt="" loading="lazy">
                        <span><input type="checkbox" value="{{ $image->id }}"> {{ $image->original_name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="sticky bottom-0 flex justify-end border-t bg-white p-5"><button type="button" data-gallery-apply class="rounded-lg bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white">Use selected images</button></div>
    </dialog>
    <noscript><p class="text-sm text-amber-700">Enable JavaScript to add, remove or reorder images. Existing selections are preserved.</p></noscript>
</div>

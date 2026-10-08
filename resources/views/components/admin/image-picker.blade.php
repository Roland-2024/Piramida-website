@props(['name' => 'featured_media_id', 'label' => 'Featured image', 'mediaItems', 'selected' => null])
@php
    $images = $mediaItems->filter(fn ($media) => $media->disk === 'public' && str_starts_with($media->mime_type, 'image/'));
    $value = old($name, $selected);
    $image = $images->firstWhere('id', $value);
@endphp
<div data-image-picker>
    <label for="{{ $name }}" class="block text-sm font-medium">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}" class="mt-2 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm">
        <option value="">No image</option>
        @foreach ($images as $media)
            <option value="{{ $media->id }}" data-url="{{ $media->url() }}" @selected((string) $value === (string) $media->id)>{{ $media->original_name }}</option>
        @endforeach
    </select>
    <img data-image-preview @if($image) src="{{ $image->url() }}" @else hidden @endif alt="{{ $label }} preview" class="mt-3 h-24 w-32 rounded-lg border border-slate-200 bg-slate-50 object-contain">
    @error($name)<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

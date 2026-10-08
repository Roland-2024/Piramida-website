@props(['name' => 'featured_media_id', 'label' => 'Featured image', 'mediaItems', 'selected' => null])
<x-admin.gallery-picker :media-items="$mediaItems" :selected="$selected" :label="$label" :name="$name" :single="true" />

@props(['name' => 'featured_media_id', 'label' => 'Featured image', 'selected' => null])
<x-admin.gallery-picker :selected="$selected" :label="$label" :name="$name" :single="true" />

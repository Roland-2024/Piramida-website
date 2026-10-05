<x-layouts.public :title="__('cms.businesses')" :language-urls="$languageUrls" :styles="['piramida-popup', 'attraction']">
<div class="pt-24">@include('public.businesses._experiences', ['businesses' => $items])</div>
@if ($items->hasPages())
<div class="template-pagination" style="background:#000">{{ $items->links() }}</div>
@endif
</x-layouts.public>

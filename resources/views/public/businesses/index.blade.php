<x-layouts.public :title="__('cms.businesses')" :language-urls="$languageUrls" :styles="['attraction','piramida-popup']">
<div class="pt-24">@include('public.businesses._experiences', ['businesses' => $items])</div>
<div class="template-pagination">{{ $items->links() }}</div>
</x-layouts.public>

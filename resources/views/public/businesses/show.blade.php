<x-layouts.public :title="$translation->seo_title ?: $translation->name" :description="$translation->seo_description ?: $translation->short_description" :language-urls="$languageUrls" :styles="['piramida-popup']" :footer="false">
@include('public.businesses._popup', ['business' => $item])
</x-layouts.public>

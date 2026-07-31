<x-layouts.public :title="__('cms.contact')" :language-urls="$languageUrls">
    <section class="mx-auto max-w-3xl px-5 py-16 lg:px-8">
        <h1 class="text-4xl font-semibold">{{ __('cms.contact') }}</h1>
        <p class="mt-3 text-slate-600">{{ __('cms.contact_intro') }}</p>
        <div class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 sm:p-8">
            @include('public.submissions._form', [
                'action' => route('public.contact.store', app()->getLocale()),
                'formType' => 'contact',
            ])
        </div>
    </section>
</x-layouts.public>

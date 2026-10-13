<x-layouts.admin title="Website content">
    <p class="mb-5 text-sm text-slate-600">Public website content in Albanian and English. Existing page/post content is still edited in its own module. Technical settings and WordPress synchronization are unchanged.</p>
    <aside class="mb-5 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-slate-700">
        <p class="font-semibold">One editing location for each piece of content</p>
        <p class="mt-2">Use <a class="underline" href="{{ route('admin.pages.index') }}">Pages</a> for page headings/SEO and <a class="underline" href="{{ route('admin.sections.index') }}">Page sections</a> for homepage/About copy. Use the individual News, Events, Programs, Attractions, Businesses and Spaces modules for their records. This screen manages shared labels, template images, default SEO and public contact details.</p>
        <p class="mt-2">A fallback is used only when the corresponding record/section field is empty. Some legacy labels are retained for older templates; saving them does not add a new element to the current design.</p>
    </aside>
    <nav class="mb-6 flex flex-wrap gap-2" aria-label="Content groups">
        @foreach($groups as $key => $label)
            <a href="{{ route('admin.website-content.edit', ['group' => $key]) }}" @class(['rounded-lg border px-3 py-2 text-sm', 'bg-slate-900 text-white' => $group === $key, 'bg-white' => $group !== $key])>{{ $label }}</a>
        @endforeach
    </nav>
    @if($errors->any())<div role="alert" class="mb-4 rounded-lg bg-red-50 p-4 text-red-800"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route($group === 'contact' ? 'admin.website-content.contact' : 'admin.website-content.update') }}" class="space-y-5">
        @csrf @method('PUT')
        <input type="hidden" name="group" value="{{ $group }}">
        @if($group === 'contact')
            <p class="text-sm text-slate-600">Public email/phone, Address, Footer text, Facebook and Instagram appear in the current templates; Map embed URL appears on Contact. X, LinkedIn and Opening hours are retained but not currently displayed. These are public details, not the notification recipient or SMTP credentials. Admin Site settings edits the same stored public values, not a second copy.</p>
            <div class="grid gap-5 rounded-xl bg-white p-5 md:grid-cols-2">
            @foreach(['email' => 'Public email', 'phone' => 'Phone', 'facebook_url' => 'Facebook URL', 'x_url' => 'X URL', 'instagram_url' => 'Instagram URL', 'linkedin_url' => 'LinkedIn URL', 'map_url' => 'Map embed URL'] as $key => $label)
                <label class="block text-sm font-medium">{{ $label }}<input name="{{ $key }}" value="{{ old($key, $settings->{$key}) }}" class="mt-2 block w-full rounded-lg border border-slate-300 p-3"></label>
            @endforeach
            @foreach(['al', 'en'] as $locale)
                @foreach(['address', 'opening_hours', 'footer_text'] as $key)
                <label class="block text-sm font-medium">{{ strtoupper($locale) }} — {{ str_replace('_', ' ', $key) }}<textarea name="translations[{{ $locale }}][{{ $key }}]" rows="3" class="mt-2 block w-full rounded-lg border border-slate-300 p-3">{{ old("translations.$locale.$key", $settings->translation($locale, false)?->{$key}) }}</textarea></label>
                @endforeach
            @endforeach
            </div>
        @elseif($group === 'images')
            <p class="text-sm text-slate-600">Upload replacement images in Media first. Choose an image below; “Original template image” restores the original. Use matching proportions, especially for leasing plans: the clickable unit positions do not change. Decorative icons are optional to replace.</p>
            <div class="grid gap-5 md:grid-cols-2">
            @foreach(config('website_images') as $path)
                @php $key = sha1($path); @endphp
                <div class="rounded-xl bg-white p-5">
                    <img src="{{ \App\Support\WebsiteContent::image($path) }}" alt="" class="mb-3 h-28 w-full rounded bg-slate-100 object-contain">
                    <label for="image-{{ $key }}" class="block text-sm font-medium break-all">{{ str_replace(['template/images/', '_'], ['', ' '], $path) }}</label>
                    @if($usage = config('content_usage.images')[$path] ?? null)<p class="mt-2 text-sm text-slate-600">{{ $usage }}</p>@endif
                    <select id="image-{{ $key }}" name="images[{{ $key }}]" class="mt-2 w-full rounded-lg border border-slate-300 p-3">
                        <option value="">Original template image</option>
                        @foreach($media as $item)<option value="{{ $item->id }}" @selected((string) old("images.$key", $images[$key] ?? '') === (string) $item->id)>{{ $item->original_name }}</option>@endforeach
                    </select>
                </div>
            @endforeach
            </div>
        @else
            <p class="text-sm text-slate-600">Empty text hides that label. Keep placeholders such as :attribute and :max unchanged. Values are plain text, not HTML.</p>
            @foreach($keys as $key)
                @php $id = sha1($key); @endphp
                <fieldset class="rounded-xl border border-slate-200 bg-white p-5">
                    <legend class="px-2 text-sm font-semibold">{{ str_replace(['website.', 'cms.', '_', '.'], ['', '', ' ', ' / '], $key) }}</legend>
                    @if($usage = config('content_usage.texts')[$key] ?? null)<p class="mb-3 rounded-lg bg-blue-50 p-3 text-sm text-slate-700">{{ $usage }} <a class="underline" href="{{ route('admin.sections.index') }}">Open Page sections</a></p>@endif
                    @if(in_array($key, config('content_usage.retained_texts', []), true))<p class="mb-3 rounded-lg bg-slate-100 p-3 text-sm text-slate-600">Retained legacy label: not displayed by the current public templates. Its saved translations are preserved; changing it does not create a new website element.</p>@endif
                    <div class="grid gap-4 md:grid-cols-2">
                    @foreach(['al', 'en'] as $locale)
                        <label class="block text-sm font-medium">{{ strtoupper($locale) }}
                            <textarea name="texts[{{ $id }}][{{ $locale }}]" rows="2" class="mt-2 block w-full rounded-lg border border-slate-300 p-3 font-normal">{{ old("texts.$id.$locale", $texts->get($key)?->get($locale) ?? $defaults[$locale][$key] ?? $defaults['en'][$key]) }}</textarea>
                        </label>
                    @endforeach
                    </div>
                </fieldset>
            @endforeach
        @endif
        <div class="sticky bottom-0 rounded-xl border border-slate-200 bg-white p-4"><button class="rounded-lg bg-slate-900 px-6 py-3 text-sm font-semibold text-white" type="submit">Save content</button></div>
    </form>
</x-layouts.admin>

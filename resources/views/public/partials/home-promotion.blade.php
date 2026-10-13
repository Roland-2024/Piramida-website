@if(!$section || $section->is_active)
    @php
        $copy = $section?->translation(app()->getLocale());
        $title = $section ? $copy?->title : $defaultTitle;
        $defaultAction = !$section || (!$section->primary_button_url && !$copy?->primary_button_label);
    @endphp
    <article class="flex flex-col overflow-hidden rounded-[20px]" style="background:{{ $color }}" data-home-promotion="{{ $section?->internal_name }}">
        <x-public-image :media="$section ? $section->primaryMedia : ($defaultMedia ?? null)" :path="$templateImage" alt="{{ $title }}" loading="lazy" sizes="(max-width: 639px) 100vw, 50vw" class="h-[300px] sm:h-[400px] w-full object-cover p-[10px] rounded-[20px]" />
        <div class="flex flex-1 flex-col py-[50px] px-[40px]">
            @if($title)<h2 class="uppercase title-36 text-black mb-2">{{ $title }}</h2>@endif
            <div class="home-attraction-description title-16 prose-content mb-5">
                @if($section){!! $copy?->description !!}@else{{ $defaultDescription }}@endif
            </div>
            <div class="mt-auto flex flex-wrap gap-3">
                @if($defaultAction || ($section->primary_button_url && $copy?->primary_button_label))
                    <a href="{{ $defaultAction ? $defaultUrl : $section->primary_button_url }}" class="inline-flex items-center gap-2 bg-white text-black title-14-bold px-[20px] py-[16px] rounded-full hover:bg-white/90 transition">
                        {{ $defaultAction ? $defaultButton : $copy->primary_button_label }}
                        <img src="{{ \App\Support\WebsiteContent::image('template/images/arrow right black.svg') }}" alt="" class="w-3.5 h-3.5 mt-1">
                    </a>
                @endif
                @if($section?->secondary_button_url && $copy?->secondary_button_label)
                    <a href="{{ $section->secondary_button_url }}" class="inline-flex items-center bg-white text-black title-14-bold px-[20px] py-[16px] rounded-full hover:bg-white/90 transition">{{ $copy->secondary_button_label }}</a>
                @endif
            </div>
        </div>
    </article>
@endif

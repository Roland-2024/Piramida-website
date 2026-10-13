@if(!$footerPartners || $footerPartners->is_active)
    @php $partnerCopy = $footerPartners?->translation(app()->getLocale()); @endphp
    <div class="mt-16 text-center md:text-left" data-footer-partners>
        @if($partnerCopy?->subtitle)<p class="mb-2 text-[11px] uppercase tracking-wide text-white/70">{{ $partnerCopy->subtitle }}</p>@endif
        @if(!$footerPartners || $partnerCopy?->title)<h2 class="text-[12px] uppercase tracking-wide">{{ $footerPartners ? $partnerCopy->title : __('website.partials_footer_partner_t_tan') }}</h2>@endif
        @if($partnerCopy?->description)<div class="prose-content mt-3 text-sm">{!! $partnerCopy->description !!}</div>@endif
        <div class="mt-4 flex flex-wrap items-center justify-center md:justify-start gap-3">
            @if($footerPartners?->gallery->isNotEmpty())
                @foreach($footerPartners->gallery as $logo)<img src="{{ $logo->displayUrl() }}" alt="{{ (app()->getLocale() === 'en' ? $logo->alt_text_en : $logo->alt_text_al) ?: $logo->original_name }}" class="h-8 w-[75px] md:h-10 md:w-[102px] object-contain" loading="lazy">@endforeach
            @else
                <img src="{{ \App\Support\WebsiteContent::image('template/images/aadffinalk-logo 1.svg') }}" class="h-8 w-[75px] md:h-10 md:w-[102px] object-contain" alt="{{ __('website.partials_footer_aadf') }}">
                <img src="{{ \App\Support\WebsiteContent::image('template/images/bashkia2 1.svg') }}" class="h-8 w-[75px] md:h-10 md:w-[102px] object-contain" alt="{{ __('website.partials_footer_bashkia_tiran') }}">
                <img src="{{ \App\Support\WebsiteContent::image('template/images/tumo-finale2 1.svg') }}" class="h-8 w-[75px] md:h-10 md:w-[102px] object-contain" alt="{{ __('website.partials_footer_tumo') }}">
            @endif
        </div>
    </div>
@endif

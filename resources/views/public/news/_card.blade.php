@php $newsTranslation = $newsItem->translation(app()->getLocale(), false); @endphp
<article
                    class="news-card shrink-0 w-full snap-center rounded-[16px] border border-white/15 p-[30px] bg-gradient-to-b from-white/[0.06] to-white/[0.01] backdrop-blur-md">
                    <a href="{{ route('public.news.show', [app()->getLocale(), $newsTranslation->slug]) }}">
                        <div class="rounded-xl">
                            <img src="{{ $newsItem->featuredMedia?->url() ?: asset('template/images/piramida_block_1.jpg') }}" alt="{{ $newsTranslation->title }}"
                                loading="lazy" class="w-full object-cover h-[265px] rounded-[16px]" />
                        </div>
                        <div class="pt-4 pb-2 px-1">
                            <div class="text-white title-22 mb-2">
                                {{ $newsTranslation->title }}
                            </div>
                            <p class="text-white/50 title-14-semibold pt-2">{{ $newsItem->published_at->format('M d, Y') }}</p>
                        </div>
                        <div class="title-16 text-[#CBFF00] pt-2 px-1 flex gap-1 items-center">
                            {{ __('cms.read_more') }}
                            <img src="/template/images/news-arrow.svg" alt="Arrow icon" class="w-4 h-4 mt-1">
                        </div>
                    </a>
                </article>

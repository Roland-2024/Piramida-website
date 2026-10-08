@props(['label', 'links'])
@php $active = collect($links)->contains(fn ($link) => request()->routeIs($link[1].'.*')); @endphp
<details class="admin-nav-group" name="admin-menu" @if($active) open @endif>
    <summary>
        <span>{{ $label }}</span>
        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="m9 5 7 7-7 7" /></svg>
    </summary>
    <div class="admin-nav-children">
        @foreach ($links as [$text, $prefix, $action])
            <a href="{{ route($prefix.'.'.$action) }}" @if(request()->routeIs($prefix.'.*')) aria-current="page" @endif class="flex items-center rounded-lg px-3 py-2.5 text-sm font-medium {{ request()->routeIs($prefix.'.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">{{ $text }}</a>
        @endforeach
    </div>
</details>

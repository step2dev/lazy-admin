<span class="relative inline-flex h-6 w-6 shrink-0 items-center justify-center" aria-hidden="true">
    @if(is_string($iconView) && view()->exists($iconView))
        <span class="[&>svg]:h-6 [&>svg]:w-6">@include($iconView)</span>
    @elseif($rawSvgIcon)
        <span class="[&>svg]:h-6 [&>svg]:w-6">{!! $svgIcon !!}</span>
    @elseif(filled($icon))
        <i class="{{ $icon }}"></i>
    @endif

    @if($badge !== null)
        <x-lazy-badge
            accent
            class="transition-all duration-200"
            :class="sidebarCompact
                ? 'badge-xs absolute -right-3 -top-2 px-1'
                : 'hidden'"
        >{{ $badge }}</x-lazy-badge>
    @endif
</span>

<span
    class="min-w-0 flex-1 truncate"
    x-show="! sidebarCompact"
    x-transition.opacity
>
    {{ $label }}
</span>

@if($badge !== null)
    <x-lazy-badge
        accent
        class="ml-auto"
        x-show="! sidebarCompact"
        x-transition.opacity
    >{{ $badge }}</x-lazy-badge>
@endif

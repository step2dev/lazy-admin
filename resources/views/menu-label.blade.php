<span class="relative inline-flex h-6 w-6 shrink-0 items-center justify-center" aria-hidden="true">
    @if($item['iconViewExists'])
        <span class="[&>svg]:h-6 [&>svg]:w-6">@include($item['iconView'])</span>
    @elseif($item['rawSvgIcon'])
        <span class="[&>svg]:h-6 [&>svg]:w-6">{!! $item['svgIcon'] !!}</span>
    @elseif($item['hasCssIcon'])
        <i class="{{ $item['icon'] }}"></i>
    @endif

    @if($item['badgeLabel'] !== null)
        <x-lazy-badge
            accent
            class="transition-all duration-200"
            :class="sidebarCompact
                ? 'badge-xs absolute -right-3 -top-2 px-1'
                : 'hidden'"
        >{{ $item['badgeLabel'] }}</x-lazy-badge>
    @endif
</span>

<span
    class="min-w-0 flex-1 truncate"
    x-show="! sidebarCompact"
    x-transition.opacity
>
    {{ $item['labelText'] }}
</span>

@if($item['badgeLabel'] !== null)
    <x-lazy-badge
        accent
        class="ml-auto"
        x-show="! sidebarCompact"
        x-transition.opacity
    >{{ $item['badgeLabel'] }}</x-lazy-badge>
@endif

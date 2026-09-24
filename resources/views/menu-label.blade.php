<span class="relative inline-flex h-6 w-6 shrink-0 items-center justify-center" aria-hidden="true">
    @if(! empty($item['icon_view']))
        <span class="[&>svg]:h-6 [&>svg]:w-6">@include($item['icon_view'])</span>
    @elseif(! empty($item['icon']))
        <i class="{{ $item['icon'] }}"></i>
    @endif

    @if(isset($item['badge']))
        <span
            class="badge badge-accent transition-all duration-200"
            :class="sidebarCompact
                ? 'badge-xs absolute -right-3 -top-2 px-1'
                : 'hidden'"
        >{{ $item['badge'] }}</span>
    @endif
</span>

<span
    class="min-w-0 flex-1 truncate"
    x-show="! sidebarCompact"
    x-transition.opacity
>
    {{ __($item['label'] ?? '') }}
</span>

@if(isset($item['badge']))
    <span
        class="badge badge-accent ml-auto"
        x-show="! sidebarCompact"
        x-transition.opacity
    >{{ $item['badge'] }}</span>
@endif

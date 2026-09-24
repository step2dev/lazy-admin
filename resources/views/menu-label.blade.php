@php
    $iconView = $item['icon_view'] ?? null;
    $icon = $item['icon'] ?? null;

    if (is_string($iconView) && ! str_contains($iconView, '::')) {
        $lazyIconView = 'lazy::'.$iconView;

        if (view()->exists($lazyIconView)) {
            $iconView = $lazyIconView;
        }
    }

    $rawSvgIcon = is_string($icon) && str_starts_with(ltrim($icon), '<svg');
@endphp

<span class="relative inline-flex h-6 w-6 shrink-0 items-center justify-center" aria-hidden="true">
    @if(is_string($iconView) && view()->exists($iconView))
        <span class="[&>svg]:h-6 [&>svg]:w-6">@include($iconView)</span>
    @elseif($rawSvgIcon)
        <span class="[&>svg]:h-6 [&>svg]:w-6">{!! $icon !!}</span>
    @elseif(filled($icon))
        <i class="{{ $icon }}"></i>
    @endif

    @if(isset($item['badge']))
        <span
            class="badge badge-accent transition-all duration-200"
            :class="sidebarCompact
                ? 'badge-xs absolute -right-3 -top-2 px-1'
                : 'hidden'"
        >{{ is_numeric($item['badge']) && (int) $item['badge'] > 99 ? '99+' : $item['badge'] }}</span>
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
    >{{ is_numeric($item['badge']) && (int) $item['badge'] > 99 ? '99+' : $item['badge'] }}</span>
@endif

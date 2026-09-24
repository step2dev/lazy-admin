@if(! empty($item['icon_view']))
    <span aria-hidden="true">@include($item['icon_view'])</span>
@elseif(! empty($item['icon']))
    <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
@endif
<span x-show="! sidebarCompact" x-transition.opacity>{{ __($item['label'] ?? '') }}</span>
@if(isset($item['badge']))
    <span
        class="badge"
        :class="sidebarCompact ? 'badge-xs absolute -right-1 -top-1' : ''"
    >{{ $item['badge'] }}</span>
@endif

@if(! empty($item['icon_view']))
    <span aria-hidden="true">@include($item['icon_view'])</span>
@elseif(! empty($item['icon']))
    <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
@endif
<span>{{ __($item['label'] ?? '') }}</span>
@if(isset($item['badge']))
    <span class="badge">{{ $item['badge'] }}</span>
@endif

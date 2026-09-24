@if(isset($item['group']))
    <li class="menu-title" x-show="! sidebarCompact" x-transition.opacity>{{ __($item['group']) }}</li>
@else
    <li>
        @php
            $target = $item['url'] ?? $item['route'] ?? null;
            $href = $target && Route::has($target) ? route($target, $item['parameters'] ?? []) : ($target ?? '#');
            $hasChildren = ! empty($item['children']);
            $active = $item['active'] ?? ($target && Route::has($target) && request()->routeIs($target));
        @endphp
        @if($hasChildren && $target)
            <div class="flex items-center relative">
                <a href="{{ $href }}" @class(['active' => $active, 'flex-1' => true])>
                    @include('lazy::menu-label', ['item' => $item])
                </a>
                <details @if($active) open @endif>
                    <summary aria-label="{{ __('Toggle :item submenu', ['item' => __($item['label'] ?? '')]) }}"></summary>
                    <ul>
                        @foreach($item['children'] as $child)
                            @include('lazy::menu-item', ['item' => $child])
                        @endforeach
                    </ul>
                </details>
            </div>
        @elseif($hasChildren)
            <details @if($active) open @endif>
                <summary>@include('lazy::menu-label', ['item' => $item])</summary>
                <ul>
                    @foreach($item['children'] as $child)
                        @include('lazy::menu-item', ['item' => $child])
                    @endforeach
                </ul>
            </details>
        @else
            <a href="{{ $href }}" @class(['active' => $active])>@include('lazy::menu-label', ['item' => $item])</a>
        @endif
    </li>
@endif

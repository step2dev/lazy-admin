@if(isset($item['group']))
    <li
        class="menu-title overflow-hidden transition-all duration-200"
        x-show="! sidebarCompact"
        x-transition.opacity
    >
        {{ __($item['group']) }}
    </li>
@else
    <li class="relative">
        @php
            $target = $item['url'] ?? $item['route'] ?? null;
            $href = $target && Route::has($target)
                ? route($target, $item['parameters'] ?? [])
                : ($target ?? '#');
            $hasChildren = ! empty($item['children']);
            $active = $item['active']
                ?? ($target && Route::has($target) && request()->routeIs($target));
            $label = __($item['label'] ?? '');
        @endphp

        @if($hasChildren && $target)
            <div class="relative flex items-center">
                <a
                    href="{{ $href }}"
                    @if(! empty($item['target'])) target="{{ $item['target'] }}" @endif
                    @if(($item['target'] ?? null) === '_blank') rel="noopener noreferrer" @endif
                    title="{{ $label }}"
                    @class([
                        'active' => $active,
                        'relative flex min-w-0 flex-1 items-center',
                        'justify-center px-2' => false,
                    ])
                    :class="sidebarCompact ? 'justify-center px-2' : ''"
                >
                    @include('lazy::menu-label', ['item' => $item])
                </a>

                <details class="shrink-0" @if($active) open @endif>
                    <summary
                        class="btn btn-ghost btn-xs relative"
                        aria-label="{{ __('Toggle :item submenu', ['item' => $label]) }}"
                        title="{{ __('Toggle :item submenu', ['item' => $label]) }}"
                        @click="if (sidebarCompact) { $event.preventDefault(); toggleSidebar(); }"
                    ></summary>

                    <ul x-show="! sidebarCompact" x-transition.opacity>
                        @foreach($item['children'] as $child)
                            @include('lazy::menu-item', ['item' => $child])
                        @endforeach
                    </ul>
                </details>
            </div>
        @elseif($hasChildren)
            <details @if($active) open @endif>
                <summary
                    class="relative"
                    title="{{ $label }}"
                    @click="if (sidebarCompact) { $event.preventDefault(); toggleSidebar(); }"
                    :class="sidebarCompact ? 'justify-center px-2' : ''"
                >
                    @include('lazy::menu-label', ['item' => $item])
                </summary>

                <ul x-show="! sidebarCompact" x-transition.opacity>
                    @foreach($item['children'] as $child)
                        @include('lazy::menu-item', ['item' => $child])
                    @endforeach
                </ul>
            </details>
        @else
            <a
                href="{{ $href }}"
                @if(! empty($item['target'])) target="{{ $item['target'] }}" @endif
                @if(($item['target'] ?? null) === '_blank') rel="noopener noreferrer" @endif
                title="{{ $label }}"
                @class(['active' => $active, 'relative'])
                :class="sidebarCompact ? 'justify-center px-2' : ''"
            >
                @include('lazy::menu-label', ['item' => $item])
            </a>
        @endif
    </li>
@endif

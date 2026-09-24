@if(isset($item['group']))
    <li
        class="menu-title overflow-hidden transition-all duration-200"
        x-show="! sidebarCompact"
        x-transition.opacity
    >
        {{ __($item['group']) }}
    </li>
@else
    @php
        $target = $item['url'] ?? $item['route'] ?? null;
        $href = $target && Route::has($target)
            ? route($target, $item['parameters'] ?? [])
            : ($target ?? '#');
        $hasChildren = ! empty($item['children']);
        $active = $item['active']
            ?? ($target && Route::has($target) && request()->routeIs($target));
        $label = __($item['label'] ?? '');
        $activePattern = $item['active_pattern'] ?? null;

        if ($activePattern !== null) {
            $active = request()->routeIs($activePattern);
        }
    @endphp

    <li
        class="relative"
        @if($hasChildren)
            x-data="{ open: {{ $active ? 'true' : 'false' }} }"
        @endif
    >
        @if($hasChildren)
            <div
                @class([
                    'flex items-center gap-1 rounded-lg',
                    'bg-base-300/60' => $active,
                ])
            >
                @if($target)
                    <a
                        href="{{ $href }}"
                        @if(! empty($item['target'])) target="{{ $item['target'] }}" @endif
                        @if(($item['target'] ?? null) === '_blank') rel="noopener noreferrer" @endif
                        title="{{ $label }}"
                        @class([
                            'active' => $active,
                            'relative flex min-w-0 flex-1 items-center',
                        ])
                        :class="sidebarCompact ? 'justify-center px-2' : ''"
                    >
                        @include('lazy::menu-label', ['item' => $item])
                    </a>
                @else
                    <button
                        type="button"
                        class="relative flex min-w-0 flex-1 items-center text-left"
                        title="{{ $label }}"
                        @click="if (sidebarCompact) { toggleSidebar(); } else { open = ! open; }"
                        :class="sidebarCompact ? 'justify-center px-2' : ''"
                    >
                        @include('lazy::menu-label', ['item' => $item])
                    </button>
                @endif

                <button
                    type="button"
                    class="btn btn-ghost btn-xs shrink-0"
                    aria-label="{{ __('Toggle :item submenu', ['item' => $label]) }}"
                    title="{{ __('Toggle :item submenu', ['item' => $label]) }}"
                    @click="if (sidebarCompact) { toggleSidebar(); } else { open = ! open; }"
                >
                    <svg
                        class="h-4 w-4 transition-transform duration-200"
                        :class="{ 'rotate-180': open }"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true"
                    >
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>
            </div>

            <ul
                class="mt-1 space-y-1 border-l border-base-content/10 pl-3"
                x-show="! sidebarCompact && open"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
            >
                @foreach($item['children'] as $child)
                    @include('lazy::menu-item', ['item' => $child])
                @endforeach
            </ul>
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

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
            $activePattern = $item['active_pattern'] ?? null;

            if ($activePattern !== null) {
                $active = request()->routeIs($activePattern);
            }
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

                <details class="shrink-0" @if($active) open @endif x-data="{ open: {{ $active ? 'true' : 'false' }} }" :open="open">
                    <summary
                        class="btn btn-ghost btn-xs relative"
                        aria-label="{{ __('Toggle :item submenu', ['item' => $label]) }}"
                        title="{{ __('Toggle :item submenu', ['item' => $label]) }}"
                        @click.prevent="if (sidebarCompact) { toggleSidebar(); } else { open = ! open; }"
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
                    </summary>

                    <ul
                        x-show="! sidebarCompact && open"
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
                </details>
            </div>
        @elseif($hasChildren)
            <details @if($active) open @endif x-data="{ open: {{ $active ? 'true' : 'false' }} }" :open="open">
                <summary
                    class="relative"
                    title="{{ $label }}"
                    @click.prevent="if (sidebarCompact) { toggleSidebar(); } else { open = ! open; }"
                    :class="sidebarCompact ? 'justify-center px-2' : ''"
                >
                    @include('lazy::menu-label', ['item' => $item])

                    <svg
                        class="h-4 w-4 shrink-0 transition-transform duration-200"
                        :class="{ 'rotate-180': open }"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        aria-hidden="true"
                    >
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </summary>

                <ul
                    x-show="! sidebarCompact && open"
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

@if(isset($item['group']))
    <li
        class="menu-title overflow-hidden transition-all duration-200"
        x-show="! sidebarCompact"
        x-transition.opacity
    >
        <span>{{ __($item['group']) }}</span>
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
        $external = is_string($href) && preg_match('/^https?:\/\//i', $href) === 1;
        $linkTarget = $item['target'] ?? ($external ? '_blank' : null);
    @endphp

    <li
        class="nav-item hover-bordered relative"
        @if($hasChildren)
            x-data="{ open: {{ $active ? 'true' : 'false' }} }"
        @endif
    >
        @if($hasChildren)
            <div
                @class([
                    'nav-link flex items-center gap-2 transition-all transform',
                    'active' => $active,
                ])
                :class="sidebarCompact ? 'justify-center px-2' : ''"
            >
                @if($target)
                    <a
                        href="{{ $href }}"
                        @if($linkTarget) target="{{ $linkTarget }}" @endif
                        @if($linkTarget === '_blank') rel="noopener noreferrer" @endif
                        title="{{ $label }}"
                        class="flex min-w-0 flex-1 items-center gap-2"
                    >
                        @include('lazy::menu-label', ['item' => $item])
                    </a>
                @else
                    <button
                        type="button"
                        title="{{ $label }}"
                        class="flex min-w-0 flex-1 items-center gap-2 text-left"
                        @click="if (sidebarCompact) { toggleSidebar(); } else { open = ! open; }"
                    >
                        @include('lazy::menu-label', ['item' => $item])
                    </button>
                @endif

                <button
                    type="button"
                    class="flex h-8 w-8 shrink-0 items-center justify-center"
                    aria-label="{{ __('Toggle :item submenu', ['item' => $label]) }}"
                    title="{{ __('Toggle :item submenu', ['item' => $label]) }}"
                    @click="if (sidebarCompact) { toggleSidebar(); } else { open = ! open; }"
                    x-show="! sidebarCompact"
                >
                    <svg
                        class="h-5 w-5 transform transition duration-200"
                        :class="{ 'rotate-180': open }"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        aria-hidden="true"
                    >
                        <path d="M18 9L12 15L6 9" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

            <ul
                class="bg-base-200 border-primary relative left-auto flex overflow-hidden border-b-2 flex-col"
                x-show="! sidebarCompact && open"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 transform scale-95"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-95"
            >
                @foreach($item['children'] as $child)
                    @include('lazy::menu-item', ['item' => $child])
                @endforeach
            </ul>
        @else
            <a
                href="{{ $href }}"
                @if($linkTarget) target="{{ $linkTarget }}" @endif
                @if($linkTarget === '_blank') rel="noopener noreferrer" @endif
                title="{{ $label }}"
                @class([
                    'nav-link flex gap-2 transition-all transform',
                    'active' => $active,
                ])
                :class="sidebarCompact ? 'justify-center px-2' : ''"
            >
                @include('lazy::menu-label', ['item' => $item])
            </a>
        @endif
    </li>
@endif

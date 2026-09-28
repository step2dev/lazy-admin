@if($item['isGroup'])
    <li
        class="menu-title overflow-hidden transition-all duration-200"
        x-show="! sidebarCompact"
        x-transition.opacity
    >
        <span>{{ $item['groupLabel'] }}</span>
    </li>
@else
    <li
        class="nav-item hover-bordered relative"
        @if($item['hasChildren'])
            x-data="{ open: {{ $item['active'] ? 'true' : 'false' }} }"
        @endif
    >
        @if($item['hasChildren'])
            <div
                class="{{ $item['parentNavClass'] }}"
                :class="sidebarCompact ? 'justify-center px-2' : ''"
            >
                @if($item['target'])
                    <a
                        href="{{ $item['href'] }}"
                        @if($item['linkTarget']) target="{{ $item['linkTarget'] }}" @endif
                        @if($item['linkTarget'] === '_blank') rel="noopener noreferrer" @endif
                        title="{{ $item['labelText'] }}"
                        class="flex min-w-0 flex-1 items-center gap-2"
                    >
                        @include('lazy::menu-label', ['item' => $item])
                    </a>
                @else
                    <button
                        type="button"
                        title="{{ $item['labelText'] }}"
                        class="flex min-w-0 flex-1 items-center gap-2 text-left"
                        @click="if (sidebarCompact) { toggleSidebar(); } else { open = ! open; }"
                    >
                        @include('lazy::menu-label', ['item' => $item])
                    </button>
                @endif

                <button
                    type="button"
                    class="flex h-8 w-8 shrink-0 items-center justify-center"
                    aria-label="{{ __('Toggle :item submenu', ['item' => $item['labelText']]) }}"
                    title="{{ __('Toggle :item submenu', ['item' => $item['labelText']]) }}"
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
                href="{{ $item['href'] }}"
                @if($item['linkTarget']) target="{{ $item['linkTarget'] }}" @endif
                @if($item['linkTarget'] === '_blank') rel="noopener noreferrer" @endif
                title="{{ $item['labelText'] }}"
                class="{{ $item['navClass'] }}"
                :class="sidebarCompact ? 'justify-center px-2' : ''"
            >
                @include('lazy::menu-label', ['item' => $item])
            </a>
        @endif
    </li>
@endif

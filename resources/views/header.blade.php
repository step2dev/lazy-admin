<header class="navbar sticky top-0 z-40 min-h-16 border-b border-base-300 bg-base-200 px-0 shadow-sm">
    <div class="flex flex-auto items-center justify-start px-4">
        <a href="{{ $dashboardUrl }}" class="flex items-center space-x-2" aria-label="{{ config('app.name') }}">
            @if($logoUrl)
                <img
                    src="{{ $logoUrl }}"
                    alt="{{ config('app.name') }}"
                    class="h-10 max-w-40 object-contain"
                />
            @else
                <svg
                    class="h-10 w-10 shrink-0"
                    viewBox="0 0 48 48"
                    role="img"
                    aria-label="{{ __('Lazy Admin') }}"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <rect width="48" height="48" rx="12" class="fill-primary"></rect>
                    <path
                        d="M13 12h6v19h11v5H13V12Zm20 0h5l7 24h-6l-1.3-5h-7.4L29 36h-6l7-24h3Zm1 7.3L31.6 26h4.8L34 19.3Z"
                        class="fill-primary-content"
                    ></path>
                </svg>
            @endif
        </a>

        <x-lazy-swap
            controlled
            rotate
            class="menu-toggle hidden md:inline-grid"
            x-bind:class="{ 'swap-active': ! sidebarCompact }"
            aria-label="{{ __('Toggle sidebar') }}"
        >
            <x-slot:on>
                <x-lazy-btn
                    type="button"
                    ghost
                    circle
                    aria-label="{{ __('Collapse sidebar') }}"
                    id="close"
                    @click="$dispatch('lazy-sidebar-toggle')"
                >
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <g>
                            <path fill="none" d="M0 0H24V24H0z"></path>
                            <path d="M21 18v2H3v-2h18zM6.596 3.904L8.01 5.318 4.828 8.5l3.182 3.182-1.414 1.414L2 8.5l4.596-4.596zM21 11v2h-9v-2h9zm0-7v2H3V4h9z"></path>
                        </g>
                    </svg>
                </x-lazy-btn>
            </x-slot:on>

            <x-slot:off>
                <x-lazy-btn
                    type="button"
                    ghost
                    circle
                    aria-label="{{ __('Expand sidebar') }}"
                    id="open"
                    @click="$dispatch('lazy-sidebar-toggle')"
                >
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <g>
                            <path fill="none" d="M0 0H24V24H0z"></path>
                            <path d="M21 18v2H3v-2h18zM17.404 3.904L22 8.5l-4.596 4.596-1.414-1.414L19.172 8.5 15.99 5.318l1.414-1.414zM12 11v2H3v-2h9zm0-7v2H3V4h9z"></path>
                        </g>
                    </svg>
                </x-lazy-btn>
            </x-slot:off>
        </x-lazy-swap>
    </div>

    <div class="flex flex-none items-center gap-2">
        <livewire:lazy-admin.header-search />
        <livewire:lazy-admin.notification-bell />

        <x-lazy-theme-switcher/>

        @if(config('lazy.localization.multi_language', false))
            <x-lazy-language-switcher/>
        @endif

        @if($user)
            <x-lazy-dropdown
                end
                width="w-56"
                content-class="mt-3 border border-base-300 shadow-xl"
            >
                <x-slot:trigger>
                    <div class="row flex cursor-pointer items-center rounded-xl transition hover:bg-base-300/60" tabindex="0" role="button">
                        <div class="hidden flex-col justify-center px-2 md:flex">
                            <span class="max-w-44 truncate text-right text-sm font-medium capitalize subpixel-antialiased">
                                {{ $user->name }}
                            </span>

                            @if(filled($workerType))
                                <span class="max-w-44 truncate text-right text-xs opacity-60">
                                    {{ $workerType }}
                                </span>
                            @endif
                        </div>

                        <x-lazy-btn type="button" ghost circle aria-label="{{ __('Account menu') }}">
                            <x-lazy-avatar
                                class="w-10 rounded-full bg-primary text-primary-content"
                                :src="$avatarUrl"
                                :alt="$user->name"
                                :placeholder-enabled="blank($avatarUrl)"
                            >
                                <span class="text-sm font-semibold">{{ $avatarInitials }}</span>
                            </x-lazy-avatar>
                        </x-lazy-btn>
                    </div>
                </x-slot:trigger>

                @if($profileUrl)
                    <li><a href="{{ $profileUrl }}">{{ __('Profile') }}</a></li>
                @endif

                @if($settingsUrl)
                    <li><a href="{{ $settingsUrl }}">{{ __('Settings') }}</a></li>
                @endif

                <li><x-lazy-btn-logout/></li>
            </x-lazy-dropdown>
        @endif
    </div>
</header>

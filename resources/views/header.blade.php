<header class="navbar bg-base-200">
    <div class="flex-auto items-center justify-start px-4">
        <a href="{{ $dashboardUrl }}" class="flex items-center space-x-2">
            <img
                src="{{ config('lazy.admin.logo', '/main.svg') }}"
                alt="{{ config('app.name') }}"
                class="h-14"
            />
        </a>

        <x-lazy-swap
            controlled
            rotate
            class="menu-toggle hidden md:inline-grid"
            :class="{ 'swap-active': ! sidebarCompact }"
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

    <div class="flex-none gap-2">
        <livewire:lazy-admin.header-search />
        <livewire:lazy-admin.notification-bell />

        <x-lazy-theme-switcher/>

        @if(config('lazy.localization.multi_language', false))
            <x-lazy-language-switcher/>
        @endif

        @if($user)
            <x-lazy-dropdown
                end
                width="w-52"
                content-class="mt-3 shadow"
            >
                <x-slot:trigger>
                    <div class="row flex cursor-pointer" tabindex="0" role="button">
                        <div class="hidden flex-col justify-center px-2 md:flex">
                            <span class="text-right text-base capitalize subpixel-antialiased">
                                {{ $user->name }}
                            </span>

                            @if(filled($workerType))
                                <span class="text-right font-serif text-sm lowercase">
                                    {{ $workerType }}
                                </span>
                            @endif
                        </div>

                        <x-lazy-btn type="button" ghost circle>
                            <x-lazy-avatar class="w-10 rounded-full" :src="$avatarUrl" :alt="$user->name" />
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

<header class="navbar bg-base-200">
    <div class="flex-auto items-center justify-start px-4">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
            <img
                src="{{ config('lazy.admin.logo', '/main.svg') }}"
                alt="{{ config('app.name') }}"
                class="h-14"
            />
        </a>

        <label
            aria-label="{{ __('Toggle sidebar') }}"
            class="btn btn-ghost btn-circle swap swap-rotate menu-toggle swap-active hidden md:inline-grid"
        >
            <button
                type="button"
                aria-label="{{ __('Collapse sidebar') }}"
                id="close"
                class="swap-on focus:outline-none focus:ring-0"
            >
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <g>
                        <path fill="none" d="M0 0H24V24H0z"></path>
                        <path
                            d="M21 18v2H3v-2h18zM6.596 3.904L8.01 5.318 4.828 8.5l3.182 3.182-1.414 1.414L2 8.5l4.596-4.596zM21 11v2h-9v-2h9zm0-7v2H3V4h9z"
                        ></path>
                    </g>
                </svg>
            </button>

            <button
                type="button"
                aria-label="{{ __('Expand sidebar') }}"
                id="open"
                class="swap-off focus:outline-none focus:ring-0"
            >
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <g>
                        <path fill="none" d="M0 0H24V24H0z"></path>
                        <path
                            d="M21 18v2H3v-2h18zM17.404 3.904L22 8.5l-4.596 4.596-1.414-1.414L19.172 8.5 15.99 5.318l1.414-1.414zM12 11v2H3v-2h9zm0-7v2H3V4h9z"
                        ></path>
                    </g>
                </svg>
            </button>
        </label>
    </div>

    <div class="flex-none gap-2">
        <div class="form-control hidden md:inline-flex">
            <input
                type="search"
                placeholder="{{ __('Search') }}"
                aria-label="{{ __('Search') }}"
                class="input input-bordered"
            />
        </div>

        <button
            type="button"
            class="btn btn-ghost btn-circle hidden md:inline-flex"
            aria-label="{{ __('Search') }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
            </svg>
        </button>

        <x-lazy-btn ghost class="btn-circle" aria-label="{{ __('Notifications') }}">
            <div class="indicator">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                    />
                </svg>

                <span class="badge badge-xs badge-primary indicator-item">
                    <span class="bg-primary absolute inline-flex h-full w-full animate-ping rounded-full opacity-75"></span>
                </span>
            </div>
        </x-lazy-btn>

        <x-lazy-theme-switcher/>

        @if(config('lazy.admin.multi_language', false))
            <x-lazy-language-switcher/>
        @endif

        @auth
            @php($user = auth()->user())

            <div class="dropdown dropdown-end">
                <div class="row flex cursor-pointer" tabindex="0" role="button">
                    <div class="hidden flex-col justify-center px-2 md:flex">
                        <span class="text-right text-base capitalize subpixel-antialiased">
                            {{ $user->name }}
                        </span>

                        @if(filled(data_get($user, 'worker_type')))
                            <span class="text-right font-serif text-sm lowercase">
                                {{ data_get($user, 'worker_type') }}
                            </span>
                        @endif
                    </div>

                    <div class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full">
                            <img
                                src="{{ data_get($user, 'avatar') ?: config('lazy.admin.avatar', '/img/admin.png') }}"
                                alt="{{ $user->name }}"
                            />
                        </div>
                    </div>
                </div>

                <ul
                    tabindex="0"
                    class="menu dropdown-content bg-base-100 rounded-box mt-3 w-52 p-2 shadow"
                >
                    @if(Route::has('profile.show'))
                        <li>
                            <a href="{{ route('profile.show') }}">{{ __('Profile') }}</a>
                        </li>
                    @endif

                    @if(Route::has('admin.setting.index'))
                        <li>
                            <a href="{{ route('admin.setting.index') }}">{{ __('Settings') }}</a>
                        </li>
                    @endif

                    <li>
                        <x-lazy-btn-logout/>
                    </li>
                </ul>
            </div>
        @endauth
    </div>
</header>

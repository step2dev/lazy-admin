<header class="navbar bg-base-200">
    <div class="flex-auto items-center justify-start px-4">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
            <x-lazy-image src="/img/logo.png" alt="{{ config('app.name') }}" class="h-12"/>
        </a>
        <label aria-label="toggle"
               class="btn btn-ghost btn-circle swap swap-rotate menu-toggle swap-active hidden md:inline-grid">
            <!-- this hidden checkbox controls the state -->
            {{--                <input type="checkbox" class="hidden"/>--}}
            <!-- hamburger icon -->
            <button aria-label="close" id="close"
                    class="swap-on focus:outline-none focus:ring-0">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <g>
                        <path fill="none" d="M0 0H24V24H0z"></path>
                        <path
                            d="M21 18v2H3v-2h18zM6.596 3.904L8.01 5.318 4.828 8.5l3.182 3.182-1.414 1.414L2 8.5l4.596-4.596zM21 11v2h-9v-2h9zm0-7v2h-9V4h9z"></path>
                    </g>
                </svg>
            </button>

            <!-- close icon -->
            <button aria-label="open" id="open"
                    class="swap-off focus:outline-none focus:ring-0">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <g>
                        <path fill="none" d="M0 0H24V24H0z"></path>
                        <path
                            d="M21 18v2H3v-2h18zM17.404 3.904L22 8.5l-4.596 4.596-1.414-1.414L19.172 8.5 15.99 5.318l1.414-1.414zM12 11v2H3v-2h9zm0-7v2H3V4h9z"></path>
                    </g>
                </svg>
            </button>
        </label>
    </div>
    <div class="flex-none gap-2">
        <button class="btn btn-ghost btn-circle">
            <div class="indicator">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>

                <span class="badge badge-xs badge-primary indicator-item">
                    <span
                        class="bg-primary absolute inline-flex h-full w-full animate-ping rounded-full opacity-75"></span>
                </span>
            </div>
        </button>
        <x-lazy-language-switcher/>
        <x-lazy-theme-switcher/>
    </div>
    <div class="dropdown dropdown-end">
        <div class="row flex cursor-pointer" tabindex="0">
            <div class="flex flex-col justify-center px-2">
                    <span
                        class="text-right text-base capitalize subpixel-antialiased">{{ auth()->user()->name }}</span>
                <span class="text-right font-serif text-sm lowercase">{{ auth()->user()->worker_type }}</span>
            </div>
            <label class="btn btn-ghost btn-circle avatar">
                <div class="w-10 rounded-full">
                    <img src="{{ auth()->user()->avatar ?? '/img/admin.png' }}"/>
                </div>
            </label>
        </div>
        <ul tabindex="0"
            class="menu dropdown-content bg-base-100 rounded-box mt-3 w-52 p-2 shadow">
            {{--<li>
                <a class="justify-between">
                    Profile
                    <span class="badge">New</span>
                </a>
            </li>
            <li>
                <a>Settings</a>
            </li>--}}
            <li>
                <x-lazy-btn-logout/>
            </li>
        </ul>
    </div>
</header>

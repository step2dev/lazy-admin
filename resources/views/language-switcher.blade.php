@if(config('lazy.localization.multi_language'))
    <div title="Change Language" class="dropdown dropdown-end z-[9999]">
        <div tabindex="0" class="btn btn-ghost gap-1 normal-case">
            <svg class="inline-block h-4 w-4 fill-current md:h-5 md:w-5" xmlns="http://www.w3.org/2000/svg" width="20"
                 height="20" viewBox="0 0 512 512">
                <path d="M363,176,246,464h47.24l24.49-58h90.54l24.49,58H480ZM336.31,362,363,279.85,389.69,362Z"></path>
                <path
                    d="M272,320c-.25-.19-20.59-15.77-45.42-42.67,39.58-53.64,62-114.61,71.15-143.33H352V90H214V48H170V90H32v44H251.25c-9.52,26.95-27.05,69.5-53.79,108.36-32.68-43.44-47.14-75.88-47.33-76.22L143,152l-38,22,6.87,13.86c.89,1.56,17.19,37.9,54.71,86.57.92,1.21,1.85,2.39,2.78,3.57-49.72,56.86-89.15,79.09-89.66,79.47L64,368l23,36,19.3-11.47c2.2-1.67,41.33-24,92-80.78,24.52,26.28,43.22,40.83,44.3,41.67L255,362Z"></path>
            </svg>
            <svg width="12px" height="12px" class="ml-1 hidden h-3 w-3 fill-current opacity-60 sm:inline-block"
                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2048 2048">
                <path d="M1799 349l242 241-1017 1017L7 590l242-241 775 775 775-775z"></path>
            </svg>
        </div>
        <div
            class="dropdown-content bg-base-200 text-base-content rounded-t-box rounded-b-box top-px mt-16 max-h-96 w-56 overflow-y-auto shadow-2xl">
            <ul class="menu gap-1 p-3" tabindex="0">
                @foreach($supportedLocales as $key => $locale)
                    <li>
                        <a href="{{ $locale['url'] }}" rel="alternate" hreflang="{{ $key  }}"
                           class="flex align-items-center{{ lazyLocalization()->getLocale() === $key ? ' active' : '' }}">
                            <span class="text-2xl">{{ $flags[$key] }}</span> {{ $locale['native'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

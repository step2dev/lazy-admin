@php
    $brandTitle = setting('admin.name', config('lazy.auth.branding.title', config('app.name')));
    $brandSubtitle = setting('admin.description', config('lazy.auth.branding.subtitle'));
    $brandLogo = setting('admin.logo', config('lazy.auth.branding.logo'));
    $brandBackground = setting('admin.background', config('lazy.auth.branding.background'));

    $resolveAsset = static function (?string $value): ?string {
        if (! filled($value)) {
            return null;
        }

        if (
            str_starts_with($value, 'http://')
            || str_starts_with($value, 'https://')
            || str_starts_with($value, '//')
            || str_starts_with($value, 'data:')
            || str_starts_with($value, '/')
        ) {
            return $value;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($value);
    };

    $brandLogoUrl = $resolveAsset(is_string($brandLogo) ? $brandLogo : null);
    $brandBackgroundUrl = $resolveAsset(is_string($brandBackground) ? $brandBackground : null);
    $resolvedHeading = $heading ?? null;

    $backUrl = lazyLocalization()->isMultiLanguage()
        ? lazyLocalization()->getLocalizedURL(lazyLocalization()->getLocale(), '/')
        : url('/');
@endphp

<x-lazy-base-layout :title="$title ?? $brandTitle">
    <div class="min-h-screen bg-base-200">
        <div class="flex min-h-screen">
            <section
                class="relative hidden min-h-screen overflow-hidden bg-base-300 bg-cover bg-center lg:flex lg:w-2/3"
                @if($brandBackgroundUrl)
                    style="background-image: url('{{ $brandBackgroundUrl }}');"
                @endif
            >
                <div class="absolute inset-0 bg-black/35"></div>

                <div class="relative z-10 flex w-full items-center px-10 py-12 xl:px-16 xl:py-14">
                    <div class="max-w-2xl text-left text-white">
                        <h1 class="text-4xl font-bold tracking-tight drop-shadow">
                            {{ $brandTitle }}
                        </h1>

                        @if(filled($brandSubtitle))
                            <p class="mt-4 max-w-xl text-base leading-7 text-white/85">
                                {{ $brandSubtitle }}
                            </p>
                        @endif
                    </div>
                </div>
            </section>

            <main class="relative flex min-h-screen w-full items-center justify-center px-6 py-12 sm:px-10 lg:w-1/3">
                <div class="absolute left-5 top-5 flex items-center gap-2">
                    @if(config('lazy.auth.ui.back_button', true))
                        <a
                            href="{{ $backUrl }}"
                            class="btn btn-ghost btn-sm gap-2"
                            aria-label="{{ __('Back') }}"
                            title="{{ __('Back') }}"
                        >
                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="m15 18-6-6 6-6"></path>
                            </svg>
                            <span class="hidden sm:inline">{{ __('Back') }}</span>
                        </a>
                    @endif
                </div>

                <div class="absolute right-5 top-5 flex items-center gap-2">
                    @if(lazyLocalization()->isMultiLanguage())
                        <x-lazy-language-switcher />
                    @endif

                    @if(config('lazy.auth.ui.theme_switcher', true))
                        <x-lazy-theme-switcher />
                    @endif
                </div>

                <div class="w-full max-w-md">
                    <div class="mb-8 text-center">
                        @if($brandLogoUrl)
                            <img
                                src="{{ $brandLogoUrl }}"
                                alt="{{ $brandTitle }}"
                                class="mx-auto mb-5 max-h-40 max-w-64 object-contain"
                                onerror="this.hidden=true"
                            />
                        @endif

                        @if(filled($resolvedHeading) || ! $brandLogoUrl)
                            <h2 class="text-3xl font-semibold tracking-tight">
                                {{ $resolvedHeading ?: $brandTitle }}
                            </h2>
                        @endif

                        @isset($description)
                            <p class="mt-3 text-sm opacity-70">{{ $description }}</p>
                        @endisset
                    </div>

                    @if(session('status'))
                        <x-lazy-alert success class="mb-5" :message="session('status')" />
                    @endif

                    @if(isset($errors) && $errors->any())
                        <x-lazy-alert error class="mb-5">
                            <ul class="list-disc space-y-1 pl-5 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-lazy-alert>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</x-lazy-base-layout>

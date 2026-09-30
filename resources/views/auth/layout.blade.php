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
@endphp

<x-lazy-base-layout :title="$title ?? $brandTitle">
    <div class="min-h-screen bg-base-200">
        <div class="grid min-h-screen lg:grid-cols-2">
            <section
                class="relative hidden min-h-screen overflow-hidden bg-base-300 lg:flex"
                @if($brandBackgroundUrl)
                    style="background-image: url('{{ $brandBackgroundUrl }}'); background-size: cover; background-position: center;"
                @endif
            >
                <div class="absolute inset-0 bg-base-300/75"></div>

                <div class="relative z-10 flex w-full items-end p-12">
                    <div class="max-w-xl">
                        @if($brandLogoUrl)
                            <img
                                src="{{ $brandLogoUrl }}"
                                alt="{{ $brandTitle }}"
                                class="mb-6 max-h-16 max-w-64 object-contain"
                                onerror="this.hidden=true"
                            />
                        @endif

                        <h1 class="text-4xl font-bold tracking-tight">
                            {{ $brandTitle }}
                        </h1>

                        @if(filled($brandSubtitle))
                            <p class="mt-4 text-lg opacity-70">
                                {{ $brandSubtitle }}
                            </p>
                        @endif
                    </div>
                </div>
            </section>

            <main class="relative flex min-h-screen items-center justify-center px-6 py-12 sm:px-10">
                @if(config('lazy.auth.ui.theme_switcher', true))
                    <div class="absolute right-5 top-5">
                        <x-lazy-theme-switcher />
                    </div>
                @endif

                <div class="w-full max-w-md">
                    <div class="mb-8 text-center lg:text-left">
                        @if($brandLogoUrl)
                            <img
                                src="{{ $brandLogoUrl }}"
                                alt="{{ $brandTitle }}"
                                class="mx-auto mb-5 max-h-14 max-w-56 object-contain lg:mx-0"
                                onerror="this.hidden=true"
                            />
                        @endif

                        <h2 class="text-3xl font-semibold tracking-tight">
                            {{ $heading ?? $brandTitle }}
                        </h2>

                        @isset($description)
                            <p class="mt-2 text-sm opacity-65">{{ $description }}</p>
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

                    <div class="w-full rounded-2xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-lazy-base-layout>

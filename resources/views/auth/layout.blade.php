<x-lazy-base-layout :title="$title ?? config('lazy.auth.branding.title', config('app.name'))">
    <div class="min-h-screen bg-base-200">
        <div class="grid min-h-screen lg:grid-cols-[minmax(0,1.2fr)_minmax(24rem,0.8fr)]">
            <section
                class="relative hidden overflow-hidden bg-base-300 lg:flex"
                @if(config('lazy.auth.branding.background'))
                    style="background-image: url('{{ config('lazy.auth.branding.background') }}'); background-size: cover; background-position: center;"
                @endif
            >
                <div class="absolute inset-0 bg-base-300/75"></div>

                <div class="relative z-10 flex w-full items-end p-12">
                    <div class="max-w-xl">
                        @if(config('lazy.auth.branding.logo'))
                            <img
                                src="{{ config('lazy.auth.branding.logo') }}"
                                alt="{{ config('lazy.auth.branding.title', config('app.name')) }}"
                                class="mb-6 max-h-16 max-w-64 object-contain"
                            />
                        @endif

                        <h1 class="text-4xl font-bold tracking-tight">
                            {{ config('lazy.auth.branding.title', config('app.name')) }}
                        </h1>

                        @if(config('lazy.auth.branding.subtitle'))
                            <p class="mt-4 text-lg opacity-70">
                                {{ config('lazy.auth.branding.subtitle') }}
                            </p>
                        @endif
                    </div>
                </div>
            </section>

            <main class="relative flex items-center justify-center px-6 py-12 sm:px-10">
                @if(config('lazy.auth.ui.theme_switcher', true))
                    <div class="absolute right-5 top-5">
                        <x-lazy-theme-switcher />
                    </div>
                @endif

                <div class="w-full max-w-md">
                    <div class="mb-8 text-center lg:text-left">
                        @if(config('lazy.auth.branding.logo'))
                            <img
                                src="{{ config('lazy.auth.branding.logo') }}"
                                alt="{{ config('lazy.auth.branding.title', config('app.name')) }}"
                                class="mx-auto mb-5 max-h-14 max-w-56 object-contain lg:mx-0"
                            />
                        @endif

                        <h2 class="text-3xl font-semibold tracking-tight">
                            {{ $heading ?? config('lazy.auth.branding.title', config('app.name')) }}
                        </h2>

                        @isset($description)
                            <p class="mt-2 text-sm opacity-65">{{ $description }}</p>
                        @endisset
                    </div>

                    @if(session('status'))
                        <x-lazy-alert success class="mb-5" :message="session('status')" />
                    @endif

                    @if($errors->any())
                        <x-lazy-alert error class="mb-5">
                            <ul class="list-disc space-y-1 pl-5 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-lazy-alert>
                    @endif

                    <div class="rounded-2xl border border-base-300 bg-base-100 p-6 shadow-sm sm:p-8">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-lazy-base-layout>

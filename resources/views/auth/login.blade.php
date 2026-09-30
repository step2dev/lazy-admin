@php
    $title = __('lazy-admin::auth.login');
    $heading = null;

    $configuredSubtitle = setting('admin.description', config('lazy.auth.branding.subtitle'));
    $description = filled($configuredSubtitle)
        ? $configuredSubtitle
        : __('lazy-admin::auth.description');

    $socialProviders = collect((array) config('lazy.socialite.services', []))
        ->filter(fn (array $service): bool => (bool) ($service['enable'] ?? false))
        ->keys()
        ->values();

    $socialLoginEnabled = (bool) config('lazy.socialite.enable', false)
        && Route::has('auth.social.login')
        && $socialProviders->isNotEmpty();
@endphp

@extends('lazy::auth.layout')

@section('content')
    <form method="POST" action="{{ Route::has('login.store') ? route('login.store') : route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-lazy-label for="email" :label="__('lazy-admin::auth.email')" />
            <x-lazy-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                class="mt-2 w-full"
            />
        </div>

        <div>
            <div class="flex items-center justify-between gap-4">
                <x-lazy-label for="password" :label="__('lazy-admin::auth.password')" />

                @if(config('lazy.auth.ui.forgot_password_link', true) && Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link link-primary text-sm">
                        {{ __('lazy-admin::auth.forgot_password') }}
                    </a>
                @endif
            </div>

            <x-lazy-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="mt-2 w-full"
            />
        </div>

        <label class="flex items-center gap-3">
            <x-lazy-checkbox id="remember" name="remember" />
            <span class="text-sm">{{ __('lazy-admin::auth.remember_me') }}</span>
        </label>

        <x-lazy-btn primary block type="submit" :label="__('lazy-admin::auth.login')" />

        @if($socialLoginEnabled)
            <div class="pt-1">
                <div class="divider text-xs uppercase tracking-wider opacity-50">
                    {{ __('lazy-admin::auth.or_continue_with') }}
                </div>

                <div class="flex flex-wrap justify-center gap-3">
                    @foreach($socialProviders as $provider)
                        <a
                            href="{{ route('auth.social.login', ['driver' => $provider]) }}"
                            class="btn btn-circle btn-ghost border border-base-300 bg-base-100"
                            title="{{ __('lazy-admin::auth.sign_in_with', ['provider' => ucfirst($provider)]) }}"
                            aria-label="{{ __('lazy-admin::auth.sign_in_with', ['provider' => ucfirst($provider)]) }}"
                        >
                            <span class="text-sm font-semibold uppercase">
                                {{ mb_substr($provider, 0, 1) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @if(config('lazy.auth.ui.register_link', true) && Route::has('register'))
            <p class="text-center text-sm opacity-70">
                {{ __('lazy-admin::auth.no_account') }}
                <a href="{{ route('register') }}" class="link link-primary">{{ __('lazy-admin::auth.register') }}</a>
            </p>
        @endif
    </form>
@endsection

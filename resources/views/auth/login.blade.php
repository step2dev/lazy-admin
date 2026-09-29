@php
    $title = __('Log in');
    $heading = __('Welcome back');
    $description = config('lazy.auth.branding.subtitle', __('Sign in to access your account'));
@endphp

@extends('lazy::auth.layout')

@section('content')
    <form method="POST" action="{{ Route::has('login.store') ? route('login.store') : route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-lazy-label for="email" :label="__('Email')" />
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
                <x-lazy-label for="password" :label="__('Password')" />

                @if(config('lazy.auth.ui.forgot_password_link', true) && Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link link-primary text-sm">
                        {{ __('Forgot your password?') }}
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
            <span class="text-sm">{{ __('Remember me') }}</span>
        </label>

        <x-lazy-btn primary block type="submit" :label="__('Log in')" />

        @if(config('lazy.auth.ui.register_link', true) && Route::has('register'))
            <p class="text-center text-sm opacity-70">
                {{ __("Don't have an account?") }}
                <a href="{{ route('register') }}" class="link link-primary">{{ __('Register') }}</a>
            </p>
        @endif
    </form>
@endsection

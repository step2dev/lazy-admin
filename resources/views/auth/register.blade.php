@php
    $title = __('Register');
    $heading = __('Create account');
    $description = __('Create a new account to continue.');
@endphp

@extends('lazy::auth.layout')

@section('content')
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-lazy-label for="name" :label="__('Name')" />
            <x-lazy-input id="name" name="name" :value="old('name')" required autofocus autocomplete="name" class="mt-2 w-full" />
        </div>

        <div>
            <x-lazy-label for="email" :label="__('Email')" />
            <x-lazy-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" class="mt-2 w-full" />
        </div>

        <div>
            <x-lazy-label for="password" :label="__('Password')" />
            <x-lazy-input id="password" type="password" name="password" required autocomplete="new-password" class="mt-2 w-full" />
        </div>

        <div>
            <x-lazy-label for="password_confirmation" :label="__('Confirm Password')" />
            <x-lazy-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="mt-2 w-full" />
        </div>

        @if(class_exists(\Laravel\Jetstream\Jetstream::class) && \Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
            <label class="flex items-start gap-3">
                <x-lazy-checkbox name="terms" id="terms" required />
                <span class="text-sm opacity-70">
                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="link link-primary">'.__('Terms of Service').'</a>',
                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="link link-primary">'.__('Privacy Policy').'</a>',
                    ]) !!}
                </span>
            </label>
        @endif

        <x-lazy-btn primary block type="submit" :label="__('Register')" />

        @if(Route::has('login'))
            <p class="text-center text-sm opacity-70">
                {{ __('Already registered?') }}
                <a href="{{ route('login') }}" class="link link-primary">{{ __('Log in') }}</a>
            </p>
        @endif
    </form>
@endsection

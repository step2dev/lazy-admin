@php
    $title = __('Forgot password');
    $heading = __('Reset your password');
    $description = __('Enter your email address and we will send you a password reset link.');
@endphp

@extends('lazy::auth.layout')

@section('content')
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-lazy-label for="email" :label="__('Email')" />
            <x-lazy-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" class="mt-2 w-full" />
        </div>

        <x-lazy-btn primary block type="submit" :label="__('Email Password Reset Link')" />

        @if(Route::has('login'))
            <a href="{{ route('login') }}" class="btn btn-ghost w-full">{{ __('Back to login') }}</a>
        @endif
    </form>
@endsection

@php
    $title = __('Reset password');
    $heading = __('Choose a new password');
    $description = __('Enter a new password for your account.');
@endphp

@extends('lazy::auth.layout')

@section('content')
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-lazy-label for="email" :label="__('Email')" />
            <x-lazy-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" class="mt-2 w-full" />
        </div>

        <div>
            <x-lazy-label for="password" :label="__('Password')" />
            <x-lazy-input id="password" type="password" name="password" required autocomplete="new-password" class="mt-2 w-full" />
        </div>

        <div>
            <x-lazy-label for="password_confirmation" :label="__('Confirm Password')" />
            <x-lazy-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="mt-2 w-full" />
        </div>

        <x-lazy-btn primary block type="submit" :label="__('Reset Password')" />
    </form>
@endsection

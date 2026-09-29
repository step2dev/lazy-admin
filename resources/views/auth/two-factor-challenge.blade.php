@php
    $title = __('Two-factor authentication');
    $heading = __('Two-factor authentication');
    $description = __('Confirm access with your authenticator code or a recovery code.');
@endphp

@extends('lazy::auth.layout')

@section('content')
    <div x-data="{ recovery: false }">
        <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-5">
            @csrf

            <div x-show="! recovery">
                <x-lazy-label for="code" :label="__('Authentication code')" />
                <x-lazy-input id="code" name="code" inputmode="numeric" autocomplete="one-time-code" autofocus x-ref="code" class="mt-2 w-full" />
            </div>

            <div x-cloak x-show="recovery">
                <x-lazy-label for="recovery_code" :label="__('Recovery Code')" />
                <x-lazy-input id="recovery_code" name="recovery_code" autocomplete="one-time-code" x-ref="recovery_code" class="mt-2 w-full" />
            </div>

            <button
                type="button"
                class="link link-primary text-sm"
                @click="recovery = ! recovery; $nextTick(() => recovery ? $refs.recovery_code.focus() : $refs.code.focus())"
                x-text="recovery ? '{{ __('Use an authentication code') }}' : '{{ __('Use a recovery code') }}'"
            ></button>

            <x-lazy-btn primary block type="submit" :label="__('Log in')" />
        </form>
    </div>
@endsection

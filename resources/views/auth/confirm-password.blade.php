@php
    $title = __('Confirm password');
    $heading = __('Confirm your password');
    $description = __('This is a secure area. Please confirm your password before continuing.');
@endphp

@extends('lazy::auth.layout')

@section('content')
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <x-lazy-label for="password" :label="__('Password')" />
            <x-lazy-input id="password" type="password" name="password" required autocomplete="current-password" autofocus class="mt-2 w-full" />
        </div>

        <x-lazy-btn primary block type="submit" :label="__('Confirm')" />
    </form>
@endsection

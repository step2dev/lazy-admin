@php
    $title = __('Verify email');
    $heading = __('Verify your email address');
    $description = __('Use the verification link we sent to your email address before continuing.');
@endphp

@extends('lazy::auth.layout')

@section('content')
    <div class="space-y-5">
        @if(session('status') === 'verification-link-sent')
            <x-lazy-alert success :message="__('A new verification link has been sent.')" />
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-lazy-btn primary block type="submit" :label="__('Resend Verification Email')" />
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-lazy-btn ghost block type="submit" :label="__('Log Out')" />
        </form>
    </div>
@endsection

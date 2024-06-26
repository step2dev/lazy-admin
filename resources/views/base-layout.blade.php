@props([
    'title' => '',
    'action' => '',
    'meta' => '',
    'styles' => '',
    'scripts' => '',
    'noscript' => '',
])
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' | '.config('app.name', 'Laravel'): config('app.name', 'Laravel') }} | Admin
        Panel</title>
    {{ $meta ?? '' }}
    @if($styles)
        {{ $styles }}
    @else
        @vite(config('lazy.admin.styles'))
    @endif
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-100 font-sans antialiased">
{{ $noscript ?? ''}}
<div class="flex flex-col">
    {{ $slot }}
</div>
@if($scripts)
    {{ $scripts }}
@else
    @vite(config('lazy.admin.scripts'))
@endif
<x-lazy-toast/>
</body>
</html>
